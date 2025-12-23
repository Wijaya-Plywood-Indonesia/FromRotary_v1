<?php

namespace App\Filament\Pages\LaporanProduksi\Transformers;

use Carbon\Carbon;
use App\Models\Target;
use Illuminate\Support\Facades\Log;

class ProduksiDataMap
{
    public static function make($collection)
    {
        $result = [];

        foreach ($collection as $item) {

            $namaMesin = $item->mesin->nama_mesin ?? 'TIDAK ADA MESIN';
            $tanggal = Carbon::parse($item->tgl_produksi)->format('d/m/Y');

            // DEFAULT VALUE – WAJIB ada sebelum if-else
            $ukuranDisplay = 'TIDAK ADA UKURAN';
            $totalHasil = 0;
            $targetHarian = 0;
            $jamKerja = 0;
            $potonganPerLembar = 0;
            $kodeUkuran = null;
            $targetModel = null;
            $ukuranId = null;

            // ---------------------------------------------------------
            // HITUNG TOTAL KENDALA (DOWNTIME) DALAM MENIT
            // LOGIKA SAMA PERSIS DENGAN TABLE GantiPisauRotariesTable
            // ---------------------------------------------------------
            $totalKendalaMenit = 0;
            $daftarKendala = [];

            if ($item->detailGantiPisauRotary && $item->detailGantiPisauRotary->count() > 0) {
                foreach ($item->detailGantiPisauRotary as $ganti) {
                    // Hanya hitung jika jam selesai sudah diisi (tidak kosong dan bukan '-')
                    if (!empty($ganti->jam_selesai_ganti) && $ganti->jam_selesai_ganti !== '-') {
                        try {
                            $mulai = Carbon::parse($ganti->jam_mulai_ganti_pisau);
                            $selesai = Carbon::parse($ganti->jam_selesai_ganti);
                            $durasiMenit = $mulai->diffInMinutes($selesai);

                            $totalKendalaMenit += $durasiMenit;

                            $daftarKendala[] = [
                                'kendala' => $ganti->jenis_kendala ?? 'Tidak disebutkan',
                                'keterangan' => $ganti->keterangan ?? '-',
                                'durasi_menit' => $durasiMenit,
                                'jam_mulai' => $mulai->format('H:i'),
                                'jam_selesai' => $selesai->format('H:i'),
                            ];
                        } catch (\Exception $e) {
                            // Skip jika ada error parsing
                            continue;
                        }
                    }
                }
            }

            // Format total downtime (sama dengan summarizer di table)
            $totalDowntimeFormatted = '';
            if ($totalKendalaMenit >= 60) {
                $jam = floor($totalKendalaMenit / 60);
                $menit = $totalKendalaMenit % 60;
                $totalDowntimeFormatted = "{$jam} Jam {$menit} Menit";
            } else {
                $totalDowntimeFormatted = "{$totalKendalaMenit} Menit";
            }

            // Format kendala untuk ditampilkan
            $kendalaText = '-';
            if (count($daftarKendala) > 0) {
                $kendalaText = implode(', ', array_map(function ($k) {
                    $text = $k['kendala'] . ' (' . $k['durasi_menit'] . ' menit: ' . $k['jam_mulai'] . '-' . $k['jam_selesai'] . ')';
                    if ($k['keterangan'] !== '-') {
                        $text .= ' - ' . $k['keterangan'];
                    }
                    return $text;
                }, $daftarKendala));
            }

            // ---------------------------------------------------------
            // 2. CEK DETAIL PALET
            // ---------------------------------------------------------
            if ($item->detailPaletRotary->isEmpty()) {

                $ukuranDisplay = 'BELUM INPUT PALET';

                Log::warning('Produksi tanpa detail palet', [
                    'id_produksi' => $item->id,
                    'mesin' => $namaMesin,
                    'tanggal' => $tanggal,
                ]);

            } else {
                $firstPalet = $item->detailPaletRotary->first();
                $ukuranId = $firstPalet?->id_ukuran;

                $totalHasil = $item->detailPaletRotary->sum('total_lembar') ?? 0;

                // Cari target
                $targetModel = Target::where('id_mesin', $item->id_mesin)
                    ->where('id_ukuran', $ukuranId)
                    ->first();

                if (!$targetModel) {
                    $targetModel = Target::where('id_mesin', $item->id_mesin)
                        ->whereNull('id_ukuran')
                        ->first();
                }

                $targetHarian = $targetModel?->target;
                $jamKerja = $targetModel?->jam;
                $potonganPerLembar = $targetModel?->potongan ?? 0;
                $kodeUkuran = $targetModel?->kode_ukuran;

                // Format kode ukuran
                if ($kodeUkuran && trim($kodeUkuran) !== '') {
                    $ukuranDisplay = preg_replace('/^(SPINDLESS|YUEQUN|MERANTI|SANJI|DRYER\s*PAGI)/i', '', $kodeUkuran);
                    $ukuranDisplay = trim($ukuranDisplay) ?: $kodeUkuran;
                } else {
                    $ukuranDisplay = 'UKURAN BELUM DISET (id: ' . $ukuranId . ')';
                }
            }

            // ========================================
            // PERHITUNGAN TARGET & JAM KERJA DENGAN KENDALA
            // ========================================

            // Konversi jam kerja ke menit
            $jamKerjaMenit = $jamKerja * 60;

            // Jam kerja efektif (setelah dikurangi kendala)
            $jamKerjaEfektifMenit = $jamKerjaMenit - $totalKendalaMenit;
            $jamKerjaEfektif = $jamKerjaEfektifMenit / 60; // dalam jam

            // Target per jam (normal)
            $targetPerJam = $jamKerja > 0 ? round($targetHarian / $jamKerja, 2) : 0;

            // Target per menit (normal)
            $targetPerMenit = $jamKerjaMenit > 0 ? round($targetHarian / $jamKerjaMenit, 4) : 0;

            // Target yang disesuaikan dengan kendala
            // Jika ada kendala, target berkurang proporsional dengan waktu yang hilang
            $targetDisesuaikan = $jamKerjaMenit > 0
                ? round(($jamKerjaEfektifMenit / $jamKerjaMenit) * $targetHarian, 2)
                : $targetHarian;

            // Selisih berdasarkan target yang disesuaikan
            $selisihProduksi = $totalHasil - $targetDisesuaikan;

            $jumlahPekerja = $item->detailPegawaiRotary->count();
            $potonganTotal = 0;
            $potonganPerOrang = 0;

            // --- LOGIKA PERHITUNGAN POTONGAN BERDASARKAN DOWNTIME ---
            // Hitung kekurangan produksi karena downtime
            $kekuranganKarenaDowntime = 0;
            if ($totalKendalaMenit > 0 && $targetPerMenit > 0) {
                // Kekurangan = target per menit × total menit downtime
                $kekuranganKarenaDowntime = $targetPerMenit * $totalKendalaMenit;
            }

            // Jika hasil produksi kurang dari target yang disesuaikan
            if ($selisihProduksi < 0 && $potonganPerLembar > 0) {
                $kekuranganTotal = abs($selisihProduksi);

                // Pisahkan: kekurangan karena downtime (tidak kena denda) vs kekurangan karena performa
                $kekuranganPerforma = $kekuranganTotal - $kekuranganKarenaDowntime;

                // Hanya kekurangan karena performa yang kena denda
                if ($kekuranganPerforma > 0) {
                    $potonganTotal = $kekuranganPerforma * $potonganPerLembar;

                    if ($jumlahPekerja > 0) {
                        $potonganPerOrangRaw = $potonganTotal / $jumlahPekerja;

                        // Logika Pembulatan Bertingkat: 0-299 -> 0 | 300-799 -> 500 | 800+ -> 1000
                        $ribuan = floor($potonganPerOrangRaw / 1000);
                        $ratusan = $potonganPerOrangRaw % 1000;

                        if ($ratusan < 300) {
                            $potonganPerOrang = $ribuan * 1000;
                        } elseif ($ratusan >= 300 && $ratusan < 800) {
                            $potonganPerOrang = ($ribuan * 1000) + 500;
                        } else {
                            $potonganPerOrang = ($ribuan + 1) * 1000;
                        }
                    }
                }
            }
            // -------------------------------------------------------

            $pekerja = $item->detailPegawaiRotary->map(function ($det) use ($potonganPerOrang) {
                return [
                    'id' => $det->pegawai->kode_pegawai ?? '-',
                    'nama' => $det->pegawai->nama_pegawai ?? '-',
                    'jam_masuk' => $det->jam_masuk ?? '-',
                    'jam_pulang' => $det->jam_pulang ?? '-',
                    'ijin' => $det->ijin ?? '-',
                    'keterangan' => $det->keterangan ?? '-',
                    // Tampilkan hasil yang sudah dibulatkan
                    'pot_target' => $potonganPerOrang,
                ];
            })->toArray();

            $result[] = [
                'mesin' => $namaMesin,
                'tanggal' => $tanggal,
                'ukuran' => $ukuranDisplay,
                'pekerja' => $pekerja,
                'kendala' => $kendalaText,
                'daftar_kendala' => $daftarKendala,
                'jam_kerja' => $jamKerja,
                'jam_kerja_efektif' => round($jamKerjaEfektif, 2),
                'total_kendala_menit' => $totalKendalaMenit,
                'total_downtime_formatted' => $totalDowntimeFormatted, // INI YANG KURANG!
                'target' => $targetDisesuaikan, // Target yang sudah disesuaikan
                'target_normal' => $targetHarian, // Target normal tanpa penyesuaian
                'target_per_jam' => $targetPerJam,
                'target_per_menit' => $targetPerMenit,
                'hasil' => $totalHasil,
                'selisih' => $selisihProduksi,
                'potongan_total' => $potonganTotal,
                'potongan_per_orang' => $potonganPerOrang, // Sudah integer hasil custom round
                'has_target' => $targetModel !== null,
                'kode_ukuran_raw' => $kodeUkuran,
                'ukuran_id' => $ukuranId,
            ];

            Log::info('ProduksiDataMap', [
                'mesin' => $namaMesin,
                'ukuran_id' => $ukuranId,
                'kode_ukuran' => $ukuranDisplay,
                'target_normal' => $targetHarian,
                'target_disesuaikan' => $targetDisesuaikan,
                'total_kendala_menit' => $totalKendalaMenit,
                'total_downtime_formatted' => $totalDowntimeFormatted,
                'jumlah_kendala' => count($daftarKendala),
                'jam_kerja_efektif' => $jamKerjaEfektif,
                'hasil' => $totalHasil,
                'selisih' => $selisihProduksi,
                'potongan_per_orang' => $potonganPerOrang,
            ]);
        }

        return $result;
    }
}
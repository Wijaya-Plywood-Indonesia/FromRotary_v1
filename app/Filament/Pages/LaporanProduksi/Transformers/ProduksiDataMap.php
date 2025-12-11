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

            // Sekarang aman – semua variabel pasti ada
            $selisihProduksi = $totalHasil - $targetHarian;

            $jumlahPekerja = $item->detailPegawaiRotary->count();
            $potonganTotal = 0;
            $potonganPerOrang = 0;

            // --- LOGIKA PERHITUNGAN POTONGAN CUSTOM (DIPERBARUI) ---
            if ($targetHarian > 0 && $selisihProduksi < 0 && $potonganPerLembar > 0) {
                // Hitung total uang denda
                $potonganTotal = abs($selisihProduksi) * $potonganPerLembar;

                if ($jumlahPekerja > 0) {
                    $potonganPerOrangRaw = $potonganTotal / $jumlahPekerja;

                    // Logika Pembulatan Bertingkat: 0-299 -> 0 | 300-799 -> 500 | 800+ -> 1000
                    $ribuan = floor($potonganPerOrangRaw / 1000);

                    // Menggunakan fmod agar sisa bagi desimal tetap akurat, atau % untuk integer standar
                    // Di sini kita gunakan % standar seperti request
                    $ratusan = $potonganPerOrangRaw % 1000;

                    if ($ratusan < 300) {
                        // Bulatkan ke bawah (misal 4.250 jadi 4.000)
                        $potonganPerOrang = $ribuan * 1000;

                    } elseif ($ratusan >= 300 && $ratusan < 800) {
                        // Bulatkan ke tengah (misal 4.350 jadi 4.500)
                        $potonganPerOrang = ($ribuan * 1000) + 500;

                    } else {
                        // Bulatkan ke atas (misal 4.850 jadi 5.000)
                        $potonganPerOrang = ($ribuan + 1) * 1000;
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
                'kendala' => $item->kendala ?? '-',
                'jam_kerja' => $jamKerja,
                'target' => $targetHarian,
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
                'target' => $targetHarian,
                'hasil' => $totalHasil,
                'potongan_per_orang' => $potonganPerOrang
            ]);
        }

        return $result;
    }
}
<?php

namespace App\Filament\Pages\LaporanHarian\Transformers;

use Carbon\Carbon;
use App\Models\Target;
use Illuminate\Support\Facades\Log;

class RepairWorkerMap
{
    public static function make($collection): array
    {
        $results = [];

        foreach ($collection as $produksi) {

            // 1. Loop Modal (Ukuran Kayu yang dikerjakan)
            foreach ($produksi->modalRepairs as $modal) {

                // --- A. KONSTRUKSI LABEL & UKURAN (Sesuai Referensi RepairDataMap) ---
                $ukuranModel = $modal->ukuran;
                $jenisKayuModel = $modal->jenisKayu;
                $kw = $modal->kw ?? $modal->kualitas ?? 1;

                // Label Visual untuk Laporan
                $labelPekerjaan = 'REPAIR';
                if ($ukuranModel) {
                    $labelPekerjaan .= ' ' . $ukuranModel->panjang . 'x' . $ukuranModel->lebar;
                }

                // Buat Kode Ukuran untuk cari Target (Logic Database Lama)
                // Format: REPAIR[Panjang][Lebar][Tebal][KW][Jenis]
                if ($ukuranModel && $jenisKayuModel) {
                    $kodeUkuran = 'REPAIR' .
                        $ukuranModel->panjang .
                        $ukuranModel->lebar .
                        str_replace('.', ',', $ukuranModel->tebal) .
                        $kw .
                        strtolower($jenisKayuModel->kode_kayu);
                } else {
                    $kodeUkuran = 'REPAIR-NOT-FOUND';
                }

                // --- B. CARI TARGET (LOGIKA 3 LEVEL PRIORITAS) ---

                // Level 1: Spesifik Kode Ukuran DAN ID Mesin
                $targetLv1 = Target::where('kode_ukuran', $kodeUkuran)
                    ->where('id_mesin', $produksi->id_mesin)
                    ->first();

                // Level 2: Spesifik Kode Ukuran saja
                $targetLv2 = Target::where('kode_ukuran', $kodeUkuran)->first();

                // Level 3: Fallback ke ID Ukuran & ID Jenis Kayu & ID Mesin
                $targetLv3 = Target::where([
                    'id_mesin' => $produksi->id_mesin,
                    'id_ukuran' => $modal->id_ukuran,
                    'id_jenis_kayu' => $modal->id_jenis_kayu,
                ])->first();

                // Pilih target yang ditemukan (Prioritas 1 -> 2 -> 3)
                $targetModel = $targetLv1 ?? $targetLv2 ?? $targetLv3;

                // Ambil Nilai Target
                $targetWajib = (int) ($targetModel->target ?? 0);
                $potonganPerLembar = (int) ($targetModel->potongan ?? 0);

                // --- C. LOOP PEGAWAI ---
                foreach ($produksi->rencanaPegawais as $rp) {

                    if (!$rp->pegawai)
                        continue;

                    // 1. Hitung Hasil Individu Pegawai di Modal ini
                    $hasilIndividu = $rp->rencanaRepairs
                        ->where('id_modal_repair', $modal->id)
                        ->flatMap->hasilRepairs
                        ->sum('jumlah');

                    // 2. Hitung Potongan
                    $selisih = $hasilIndividu - $targetWajib;
                    $potonganPerOrang = 0;

                    // Jika hasil kurang dari target, hitung denda
                    if ($selisih < 0 && $targetWajib > 0 && $potonganPerLembar > 0) {
                        $nominalPotongan = abs($selisih) * $potonganPerLembar;

                        // Pembulatan Khusus (0, 500, 1000)
                        $ribuan = floor($nominalPotongan / 1000);
                        $ratusan = $nominalPotongan % 1000;

                        if ($ratusan < 300) {
                            $potonganPerOrang = $ribuan * 1000;
                        } elseif ($ratusan < 800) {
                            $potonganPerOrang = ($ribuan * 1000) + 500;
                        } else {
                            $potonganPerOrang = ($ribuan + 1) * 1000;
                        }
                    }

                    // Prioritas: Input Manual > Rumus
                    $potonganFinal = $rp->potongan ?? $potonganPerOrang;

                    // 3. Masukkan ke Array Hasil
                    $results[] = [
                        'kodep' => $rp->pegawai->kode_pegawai ?? '-',
                        'nama' => $rp->pegawai->nama_pegawai ?? 'TANPA NAMA',
                        'masuk' => $rp->jam_masuk ? Carbon::parse($rp->jam_masuk)->format('H:i') : '',
                        'pulang' => $rp->jam_pulang ? Carbon::parse($rp->jam_pulang)->format('H:i') : '',
                        'hasil' => $labelPekerjaan,
                        'ijin' => $rp->ijin ?? '',
                        'potongan_targ' => (int) $potonganFinal,
                        'keterangan' => $rp->keterangan ?? '',
                    ];
                }
            }
        }

        return $results;
    }
}
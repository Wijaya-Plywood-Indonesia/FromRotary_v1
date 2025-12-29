<?php

namespace App\Filament\Pages\LaporanHarian\Transformers;

use Carbon\Carbon;
use App\Models\Target;

class RepairWorkerMap
{
    public static function make($collection): array
    {
        $results = [];

        foreach ($collection as $produksi) {

            // 1. Loop Modal (Ukuran Kayu yang dikerjakan)
            // Di Repair, target biasanya melekat pada ukuran kayu (Modal)
            foreach ($produksi->modalRepairs as $modal) {

                // --- A. KONSTRUKSI LABEL & UKURAN ---
                $ukuranModel = $modal->ukuran;
                $jenisKayuModel = $modal->jenisKayu;
                $kw = $modal->kw ?? $modal->kualitas ?? 1;

                // Label untuk kolom 'hasil' di Excel
                // Contoh output: "REPAIR - 122x244" atau "SANDING JOI"
                $labelPekerjaan = 'REPAIR';
                if ($ukuranModel) {
                    $labelPekerjaan .= ' ' . $ukuranModel->panjang . 'x' . $ukuranModel->lebar;
                }

                // Buat Kode Ukuran untuk cari Target (Logic Database Lama)
                // Format: REPAIR[Panjang][Lebar][Tebal][KW][Jenis]
                $kodeUkuran = 'REPAIR-NOT-FOUND';
                if ($ukuranModel && $jenisKayuModel) {
                    $kodeUkuran = 'REPAIR' .
                        $ukuranModel->panjang .
                        $ukuranModel->lebar .
                        str_replace('.', ',', $ukuranModel->tebal) .
                        $kw .
                        strtolower($jenisKayuModel->kode_kayu);
                }

                // --- B. CARI TARGET ---
                // Cek Level 1 (Spesifik Mesin) -> Level 2 (Umum) -> Level 3 (ID)
                $targetModel = Target::where('kode_ukuran', $kodeUkuran)
                    ->where('id_mesin', $produksi->id_mesin)
                    ->first();

                if (!$targetModel) {
                    $targetModel = Target::where('kode_ukuran', $kodeUkuran)->first();
                }

                // Ambil Nilai Target
                $targetWajib = (int) ($targetModel->target ?? 0);
                $potonganPerLembar = (int) ($targetModel->potongan ?? 0);

                // --- C. LOOP PEGAWAI ---
                foreach ($produksi->rencanaPegawais as $rp) {

                    // Anti Crash: Skip jika data pegawai master terhapus
                    if (!$rp->pegawai)
                        continue;

                    // 1. Hitung Hasil Individu Pegawai ini di Modal ini
                    $hasilIndividu = $rp->rencanaRepairs
                        ->where('id_modal_repair', $modal->id)
                        ->flatMap->hasilRepairs
                        ->sum('jumlah');

                    // 2. Hitung Potongan
                    $selisih = $hasilIndividu - $targetWajib;
                    $nominalPotongan = 0;

                    // Jika hasil kurang dari target, hitung denda
                    if ($selisih < 0 && $targetWajib > 0 && $potonganPerLembar > 0) {
                        $nominalPotongan = abs($selisih) * $potonganPerLembar;
                    }

                    // Prioritas: Jika ada input manual potongan, pakai itu. Jika tidak, pakai rumus.
                    $potonganFinal = $rp->potongan ?? $nominalPotongan;

                    // 3. Masukkan ke Array Hasil
                    $results[] = [
                        'kodep' => $rp->pegawai->kode_pegawai ?? '-',
                        'nama' => $rp->pegawai->nama_pegawai ?? 'TANPA NAMA',

                        'masuk' => $rp->jam_masuk
                            ? Carbon::parse($rp->jam_masuk)->format('H:i')
                            : '',

                        'pulang' => $rp->jam_pulang
                            ? Carbon::parse($rp->jam_pulang)->format('H:i')
                            : '',

                        'hasil' => $labelPekerjaan, // Contoh: REPAIR 1220x2440

                        'ijin' => $rp->ijin ?? '',

                        'potongan_targ' => $potonganFinal, // Integer

                        'keterangan' => $rp->keterangan ?? '',
                    ];
                }
            }
        }

        return $results;
    }
}
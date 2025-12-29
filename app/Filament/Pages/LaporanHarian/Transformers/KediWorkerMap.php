<?php

namespace App\Filament\Pages\LaporanHarian\Transformers;

use Carbon\Carbon;
use App\Models\Target;

class KediWorkerMap
{
    public static function make($collection): array
    {
        $results = [];

        // 1. Ambil Referensi Target KEDI
        // Pastikan di tabel targets ada data: kode_ukuran = 'KEDI'
        $targetRef = Target::where('kode_ukuran', 'KEDI')->first();

        $stdTarget = $targetRef->target ?? 0;
        $stdPotHarga = $targetRef->potongan ?? 0;

        foreach ($collection as $produksi) {

            $labelDivisi = "KEDI / PUTTY";

            // 2. Hitung Total Hasil Produksi
            // Logika diambil dari LaporanKedi.php: Cek status atau isi detailnya
            $totalHasil = 0;

            // Jika ada detail bongkar, utamakan itu (Output)
            if ($produksi->detailBongkarKedi && $produksi->detailBongkarKedi->isNotEmpty()) {
                $totalHasil = $produksi->detailBongkarKedi->sum('jumlah');
            }
            // Jika tidak, cek detail masuk (Input)
            elseif ($produksi->detailMasukKedi && $produksi->detailMasukKedi->isNotEmpty()) {
                $totalHasil = $produksi->detailMasukKedi->sum('jumlah');
            }

            // 3. Hitung Selisih & Potongan (Logika Standar)
            $selisih = $stdTarget - $totalHasil;
            $potonganPerOrang = 0;

            // Jika hasil kurang dari target DAN target/potongan diset
            if ($selisih > 0 && $stdTarget > 0 && $stdPotHarga > 0) {

                $jumlahPekerja = $produksi->detailPegawaiKedi ? $produksi->detailPegawaiKedi->count() : 0;

                if ($jumlahPekerja > 0) {
                    $totalPot = $selisih * $stdPotHarga;
                    $potonganRaw = $totalPot / $jumlahPekerja;

                    // --- RUMUS PEMBULATAN KHUSUS (0, 500, 1000) ---
                    $ribuan = floor($potonganRaw / 1000);
                    $ratusan = $potonganRaw % 1000;

                    if ($ratusan < 300) {
                        $potonganPerOrang = $ribuan * 1000;
                    } elseif ($ratusan < 800) {
                        $potonganPerOrang = ($ribuan * 1000) + 500;
                    } else {
                        $potonganPerOrang = ($ribuan + 1) * 1000;
                    }
                }
            }

            // 4. Mapping Pegawai
            // Pastikan relasi 'detailPegawaiKedi' ada di Model ProduksiKedi
            if ($produksi->detailPegawaiKedi) {
                foreach ($produksi->detailPegawaiKedi as $dp) {

                    if (!$dp->pegawai)
                        continue;

                    $jamMasuk = $dp->masuk ? Carbon::parse($dp->masuk)->format('H:i') : '';
                    $jamPulang = $dp->pulang ? Carbon::parse($dp->pulang)->format('H:i') : '';

                    // Prioritas: Input Manual > Rumus
                    $potonganFinal = $dp->potongan ?? $potonganPerOrang;

                    $results[] = [
                        'kodep' => $dp->pegawai->kode_pegawai ?? '-',
                        'nama' => $dp->pegawai->nama_pegawai ?? 'TANPA NAMA',
                        'masuk' => $jamMasuk,
                        'pulang' => $jamPulang,
                        'hasil' => $labelDivisi,
                        'ijin' => $dp->ijin ?? '',
                        'potongan_targ' => (int) $potonganFinal,
                        'keterangan' => $dp->keterangan ?? '',
                    ];
                }
            }
        }

        return $results;
    }
}
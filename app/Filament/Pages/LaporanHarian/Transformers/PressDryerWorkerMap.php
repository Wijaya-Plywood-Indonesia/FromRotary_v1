<?php

namespace App\Filament\Pages\LaporanHarian\Transformers;

use Carbon\Carbon;
use App\Models\Target;

class PressDryerWorkerMap
{
    public static function make($collection): array
    {
        $results = [];

        foreach ($collection as $item) {

            // 1. Label Divisi
            $namaMesin = '-';
            if ($item->detailMesins && $item->detailMesins->isNotEmpty()) {
                $firstMesin = $item->detailMesins->first();
                $namaMesin = $firstMesin->mesin->nama_mesin ?? $firstMesin->kategoriMesin->nama_kategori_mesin ?? 'MESIN ?';
            }
            $labelDivisi = "PRESS DRYER - " . strtoupper($namaMesin);

            // 2. Hitung Downtime (Jika ada tabel kendala, masukkan disini. Default 0)
            $totalKendalaMenit = 0;
            // logic kendala press dryer disini jika ada...

            // 3. Hitung Target & Potongan
            $totalHasil = 0;
            if ($item->detailHasils) {
                $totalHasil = $item->detailHasils->sum('isi'); // Jumlah lembar
            }

            // Cari target berdasarkan ID Mesin Dryer
            $idMesin = $item->detailMesins->first()->id_mesin_dryer ?? null;

            $targetModel = null;
            if ($idMesin) {
                $targetModel = Target::where('id_mesin', $idMesin)->first();
            }

            $targetHarian = (int) ($targetModel->target ?? 0);
            $jamKerja = $targetModel?->jam ?? 0;
            $potonganPerLembar = (int) ($targetModel->potongan ?? 0);

            // Hitung Target Disesuaikan (Efektif)
            $jamKerjaMenit = $jamKerja * 60;
            $jamKerjaEfektifMenit = $jamKerjaMenit - $totalKendalaMenit;

            $targetDisesuaikan = ($jamKerjaMenit > 0)
                ? round(($jamKerjaEfektifMenit / $jamKerjaMenit) * $targetHarian, 2)
                : $targetHarian;

            $selisihProduksi = $totalHasil - $targetDisesuaikan;
            $potonganPerOrang = 0;

            // Logika Potongan (Sama persis dengan Rotary)
            if ($selisihProduksi < 0 && $potonganPerLembar > 0) {
                $targetPerMenit = $jamKerjaMenit > 0 ? ($targetHarian / $jamKerjaMenit) : 0;
                $kekuranganToleransi = $targetPerMenit * $totalKendalaMenit;

                $kekuranganTotal = abs($selisihProduksi);
                $kekuranganPerforma = $kekuranganTotal - $kekuranganToleransi;

                if ($kekuranganPerforma > 0) {
                    $jumlahPekerja = $item->detailPegawais->count();
                    if ($jumlahPekerja > 0) {
                        $potonganTotal = $kekuranganPerforma * $potonganPerLembar;
                        $potonganRaw = $potonganTotal / $jumlahPekerja;

                        // Pembulatan Khusus (0, 500, 1000)
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
            }

            // 4. Mapping Pegawai
            foreach ($item->detailPegawais as $dp) {
                if (!$dp->pegawai)
                    continue;

                $jamMasuk = $dp->masuk ? Carbon::parse($dp->masuk)->format('H:i') : '';
                $jamPulang = $dp->pulang ? Carbon::parse($dp->pulang)->format('H:i') : '';

                // Prioritas potongan manual jika ada
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

        return $results;
    }
}
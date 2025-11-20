<?php

namespace App\Filament\Pages\LaporanProduksi\Transformers;

use Carbon\Carbon;
use App\Models\Target;

/**
 * Class ProduksiDataMap
 *
 * Transformer ini bertugas mengubah data model Produksi menjadi struktur array
 * yang siap dikonsumsi oleh blade view pada halaman Laporan Produksi.
 *
 * Setiap elemen dalam collection diubah menjadi data terstruktur yang mencakup:
 * - Informasi mesin
 * - Tanggal produksi
 * - Ukuran pallet
 * - Total hasil produksi
 * - Target produksi & selisih
 * - Jumlah pekerja dan info detail pekerja
 * 
 * Developer selanjutnya dapat memahami alur melalui komentar berlapis di dalam fungsi.
 */
class ProduksiDataMap
{
    /**
     * Transform collection menjadi data siap tampil.
     *
     * @param \Illuminate\Support\Collection $collection
     * @return array
     */
    public static function make($collection)
    {
        $result = [];

        foreach ($collection as $item) {

            // ---------------------------------------------------------
            // 1. INFORMASI DASAR PRODUKSI
            // ---------------------------------------------------------

            $namaMesin = $item->mesin->nama_mesin;
            $tanggal = Carbon::parse($item->tgl_produksi)->format('d/m/Y');

            // ---------------------------------------------------------
            // 2. DETAIL PALET (UKURAN & TOTAL PRODUKSI)
            // ---------------------------------------------------------

            $firstPalet = $item->detailPaletRotary->first();
            $ukuran = $firstPalet?->id_ukuran ?? 'TIDAK ADA UKURAN';

            // Total hasil produksi (lembar)
            $totalHasil = $item->detailPaletRotary->sum('total_lembar') ?? 0;

            // ---------------------------------------------------------
            // 3. TARGET PRODUKSI (BERDASARKAN MESIN & UKURAN)
            // ---------------------------------------------------------

            $targetModel = Target::where('id_mesin', $item->id_mesin)
                ->where('id_ukuran', $ukuran)
                ->first();

            $targetHarian = $targetModel?->target ?? 0;

            // Jam kerja diambil dari model produksi
            $jamKerja = $targetModel?->jam ?? 0;

            // Target per jam (hindari pembagian nol)
            $targetPerJam = $jamKerja > 0 ? $targetHarian / $jamKerja : 0;

            // ---------------------------------------------------------
            // 4. HITUNG SELISIH PRODUKSI (HASIL - TARGET)
            // ---------------------------------------------------------

            $selisihProduksi = $totalHasil - $targetHarian;

            // ---------------------------------------------------------
            // 5. HITUNG POTONGAN GAJI JIKA TARGET TIDAK TERCAPAI
            // ---------------------------------------------------------

            $jumlahPekerja = $item->detailPegawaiRotary->count();
            $potonganPerLembar = $targetModel?->potongan ?? 0;

            $potonganTotal = 0;
            $potonganPerOrang = 0;

            /**
             * Jika selisih negatif → target tidak tercapai
             * Maka pekerja mendapat potongan
             */
            if ($selisihProduksi < 0) {
                $potonganTotal = abs($selisihProduksi) * $potonganPerLembar;

                $potonganPerOrang = $jumlahPekerja > 0
                    ? $potonganTotal / $jumlahPekerja
                    : 0;
            }

            // ---------------------------------------------------------
            // 6. DETAIL PEKERJA (UNTUK TAMPILAN TABEL)
            // ---------------------------------------------------------

            /**
             * Catatan:
             * "selisih produksi" tidak dimasukkan ke pekerja[]
             * karena itu merupakan perhitungan global produksi.
             * 
             * Hanya potongan per orang yang relevan bagi pekerja.
             */
            $pekerja = $item->detailPegawaiRotary->map(function ($det) use ($potonganPerOrang) {
                return [
                    'id' => $det->pegawai->kode_pegawai ?? '-',
                    'nama' => $det->pegawai->nama_pegawai ?? '-',
                    'jam_masuk' => $det->jam_masuk,
                    'jam_pulang' => $det->jam_pulang,
                    'ijin' => $det->ijin ?? '-',
                    'keterangan' => $det->keterangan ?? '-',

                    // Potongan final per orang
                    'pot_target' => $potonganPerOrang,
                ];
            })->toArray();

            // ---------------------------------------------------------
            // 7. SUSUN DATA FINAL UNTUK VIEW
            // ---------------------------------------------------------

            $result[] = [
                'mesin' => $namaMesin,
                'tanggal' => $tanggal,
                'ukuran' => $ukuran,
                'pekerja' => $pekerja,
                'kendala' => $item->kendala ?? '-',

                // Footer
                'jam_kerja' => $jamKerja,
                'target' => $targetHarian,
                'hasil' => $totalHasil,
                'selisih' => $selisihProduksi,  // ← dipakai di view, TIDAK DI DALAM pekerja[]
                'potongan_total' => $potonganTotal,
                'potongan_per_orang' => $potonganPerOrang,
            ];
        }

        return $result;
    }
}

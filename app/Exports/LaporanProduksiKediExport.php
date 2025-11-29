<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class LaporanProduksiKediExport implements FromCollection, WithTitle, ShouldAutoSize
{
    protected Collection $dataKedi;

    public function __construct(array $dataKedi)
    {
        $this->dataKedi = collect($dataKedi);
    }

    public function collection(): Collection
    {
        $rows = collect();

        if ($this->dataKedi->isEmpty()) {
            return $rows;
        }

        // =============================
        // HEADER UTAMA
        // =============================
        $rows->push(['LAPORAN PRODUKSI KEDI']);
        $rows->push(['Tanggal Produksi:', $this->dataKedi->first()['tanggal_produksi']]);
        $rows->push([]);

        foreach ($this->dataKedi as $index => $produksi) {

            // =============================
            // HEADER PRODUKSI
            // =============================
            $rows->push(['PRODUKSI KE-', $index + 1]);
            $rows->push(['Tanggal Produksi', $produksi['tanggal_produksi']]);
            $rows->push(['Status Produksi', strtoupper($produksi['status'])]);
            $rows->push(['Status Validasi', $produksi['validasi_terakhir']]);
            $rows->push(['Validator', $produksi['validasi_oleh']]);
            $rows->push([]);

            // =============================
            // DETAIL MASUK
            // =============================
            if (!empty($produksi['detail_masuk'])) {

                $rows->push(['DETAIL MASUK KEDI']);
                $rows->push([
                    'No Palet',
                    'Mesin',
                    'Ukuran',
                    'Jenis Kayu',
                    'KW',
                    'Jumlah',
                    'Rencana Bongkar',
                ]);

                foreach ($produksi['detail_masuk'] as $d) {
                    $rows->push([
                        $d['no_palet'],
                        $d['mesin'],
                        $d['ukuran'],
                        $d['jenis_kayu'],
                        $d['kw'],
                        $d['jumlah'],
                        $d['rencana_bongkar'],
                    ]);
                }

                $rows->push([]);
            }

            // =============================
            // DETAIL BONGKAR
            // =============================
            if (!empty($produksi['detail_bongkar'])) {

                $rows->push(['DETAIL BONGKAR KEDI']);
                $rows->push([
                    'No Palet',
                    'Mesin',
                    'Ukuran',
                    'Jenis Kayu',
                    'KW',
                    'Jumlah',
                ]);

                foreach ($produksi['detail_bongkar'] as $d) {
                    $rows->push([
                        $d['no_palet'],
                        $d['mesin'],
                        $d['ukuran'],
                        $d['jenis_kayu'],
                        $d['kw'],
                        $d['jumlah'],
                    ]);
                }

                $rows->push([]);
            }

            // SPASI ANTAR PRODUKSI
            $rows->push([]);
        }

        return $rows;
    }

    public function title(): string
    {
        return 'Laporan Produksi Kedi';
    }
}

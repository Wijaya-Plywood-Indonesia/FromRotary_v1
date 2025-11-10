<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class LaporanProduksiExport implements FromCollection, WithHeadings, WithTitle
{
    protected $dataProduksi;

    public function __construct($dataProduksi)
    {
        $this->dataProduksi = collect($dataProduksi)->groupBy('mesin');
    }

    public function collection()
    {
        $rows = collect();

        foreach ($this->dataProduksi as $mesinNama => $produksiList) {
            $first = $produksiList->first();
            $pekerja = $first['pekerja'] ?? [];
            $kendala = $first['kendala'] ?? 'Tidak ada kendala.';
            $tanggal = $first['tanggal'] ?? '';
            $target = $first['target'] ?? 0;
            $jamKerja = $first['jam_kerja'] ?? 0;
            $targetPerJam = $first['target_per_jam'] ?? 0;
            $hasil = $first['total_target_harian'] ?? 0;
            $selisih = $first['selisih'] ?? 0;

            // HEADER
            $rows->push(['MESIN: ' . strtoupper($mesinNama)]);
            $rows->push(['TANGGAL: ' . $tanggal]);
            $rows->push([]); // spasi

            // HEADER TABEL
            $rows->push([
                'ID',
                'Nama',
                'Masuk',
                'Pulang',
                'Ijin',
                'Potongan Target',
                'Keterangan',
                '', // spasi
                'Target Harian',
                'Jam Kerja',
                'Target/Jam',
                'Hasil',
                'Selisih',
                'Kendala'
            ]);

            // DATA PEKERJA
            foreach ($pekerja as $p) {
                $rows->push([
                    $p['id'] ?? '-',
                    $p['nama'] ?? '-',
                    $p['jam_masuk'] ?? '-',
                    $p['jam_pulang'] ?? '-',
                    $p['ijin'] ?? '-',
                    $p['pot_target'] ?? '-',
                    $p['keterangan'] ?? '-',
                    '', // spasi
                    number_format($target),
                    $jamKerja,
                    number_format($targetPerJam, 2),
                    number_format($hasil),
                    $selisih >= 0 ? '+' . number_format($selisih) : number_format($selisih),
                    $kendala
                ]);
            }

            // TOTAL
            $totalPekerja = count($pekerja);
            $totalPotongan = collect($pekerja)->sum(fn($p) => (float) str_replace('.', '', $p['pot_target'] ?? 0));

            $rows->push([
                'TOTAL',
                '',
                '',
                '',
                '',
                number_format($totalPotongan, 0, '', '.'),
                '',
                '',
                number_format($target),
                $jamKerja,
                number_format($targetPerJam, 2),
                number_format($hasil),
                $selisih >= 0 ? '+' . number_format($selisih) : number_format($selisih),
                '',
                $totalPekerja . ' pekerja'
            ]);

            $rows->push([]); // spasi
            $rows->push([]);
        }

        return $rows;
    }

    public function headings(): array
    {
        return [];
    }

    public function title(): string
    {
        return 'Laporan Produksi Rotary';
    }
}
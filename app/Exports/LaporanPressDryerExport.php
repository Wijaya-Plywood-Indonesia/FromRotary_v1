<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class LaporanPressDryerExport implements FromCollection, WithHeadings, WithTitle
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
            $hasil = $first['hasil'] ?? 0;
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
                'Hasil',
                'Selisih',
                'Kendala'
            ]);

            // DATA PEKERJA
            foreach ($pekerja as $p) {
                $potTargetRaw = (float) str_replace('.', '', $p['pot_target'] ?? '0');
                $rows->push([
                    $p['id'] ?? '-',
                    $p['nama'] ?? '-',
                    $p['jam_masuk'] ?? '-',
                    $p['jam_pulang'] ?? '-',
                    $p['ijin'] ?? '-',
                    $potTargetRaw > 0 ? (int) $potTargetRaw : '-', // TANPA TITIK
                    $p['keterangan'] ?? '-',
                    '', // spasi
                    (int) $target,                    // TANPA TITIK
                    (int) $jamKerja,                  // TANPA TITIK
                    (int) $hasil,                     // TANPA TITIK
                    $selisih >= 0 ? '+' . (int) abs($selisih) : (int) $selisih, // TANPA TITIK
                    $kendala
                ]);
            }

            // TOTAL
            $totalPekerja = count($pekerja);
            $totalPotongan = collect($pekerja)->sum(fn($p) => (float) str_replace('.', '', $p['pot_target'] ?? '0'));

            $rows->push([
                'TOTAL',
                '',
                '',
                '',
                '',
                $totalPotongan > 0 ? (int) $totalPotongan : '',
                '',
                '',
                (int) $target,
                (int) $jamKerja,
                (int) $hasil,
                $selisih >= 0 ? '+' . (int) abs($selisih) : (int) $selisih,
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
        return 'Laporan Press Dryer';
    }
}
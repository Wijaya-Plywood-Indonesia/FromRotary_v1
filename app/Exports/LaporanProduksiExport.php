<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class LaporanProduksiExport implements FromCollection, WithHeadings, WithStyles, WithTitle
{
    protected $dataProduksi;

    public function __construct($dataProduksi)
    {
        $this->dataProduksi = $dataProduksi;
    }

    public function collection()
    {
        $rows = collect();

        foreach ($this->dataProduksi as $data) {
            $bahan = $data['bahan'] ?? [];
            $hasil = $data['hasil'] ?? [];
            $pekerja = $data['pekerja'] ?? [];
            $kendala = $data['kendala'] ?? 'Tidak ada kendala.';
            $summary = $data['summary'] ?? [];

            $maxRows = max(
                count($bahan),
                collect($hasil)->sum(fn($list) => count($list)),
                count($pekerja),
                1
            );

            $hasilFlat = collect();
            foreach ($hasil as $ukuran => $kwList) {
                foreach ($kwList as $kw => $item) {
                    $hasilFlat->push([
                        'ukuran' => $ukuran,
                        'kw' => $kw,
                        'palet' => $item['palet'] ?? 0,
                        'lembar' => $item['lembar'] ?? 0
                    ]);
                }
            }

            for ($i = 0; $i < $maxRows; $i++) {
                $items = $hasilFlat->skip($i * 2)->take(2);
                $item1 = $items->get(0);
                $item2 = $items->get(1);

                $row = [
                    $i === 0 ? $data['tanggal'] : '',
                    $i === 0 ? $data['mesin'] : '',
                    $bahan[$i]['lahan'] ?? '',
                    $bahan[$i]['batang'] ?? '',
                    $item1['ukuran'] ?? '',
                    $item1 ? 'KW ' . $item1['kw'] : '',
                    $item1['palet'] ?? '',
                    $item1['lembar'] ?? '',
                    $item2['ukuran'] ?? '',
                    $item2 ? 'KW ' . $item2['kw'] : '',
                    $item2['palet'] ?? '',
                    $item2['lembar'] ?? '',
                    $pekerja[$i]['nama'] ?? '',
                    $pekerja[$i]['jam_masuk'] ?? '',
                    $pekerja[$i]['jam_pulang'] ?? '',
                    $pekerja[$i]['ijin'] ?? '',
                    $pekerja[$i]['pot_target'] ?? '',
                    $pekerja[$i]['keterangan'] ?? '',
                    $i === 0 ? $kendala : '',
                    $i === 0 ? 'Batang' : '',
                    $i === 0 ? ($summary['total_batang'] ?? 0) : '',
                    $i === 0 ? 'Palet' : '',
                    $i === 0 ? ($summary['total_palet'] ?? 0) : '',
                    $i === 0 ? 'Lembar' : '',
                    $i === 0 ? ($summary['total_lembar'] ?? 0) : '',
                    $i === 0 ? 'M³' : '',
                    $i === 0 ? number_format($summary['total_m3'] ?? 0, 3) : '',
                    $i === 0 ? 'Jam Kerja' : '',
                    $i === 0 ? ($summary['jam_kerja'] ?? 0) : '',
                    $i === 0 ? 'Pekerja' : '',
                    $i === 0 ? ($summary['jumlah_pekerja'] ?? 0) : '',
                ];

                $rows->push($row);
            }

            $rows->push(array_fill(0, 34, '')); // spasi antar hari
        }

        return $rows;
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'Mesin',
            'Lahan',
            'Batang',
            'Ukuran 1',
            'KW 1',
            'Palet 1',
            'Lembar 1',
            'Ukuran 2',
            'KW 2',
            'Palet 2',
            'Lembar 2',
            'Nama',
            'Masuk',
            'Pulang',
            'Ijin',
            'Target',
            'Ket',
            'Kendala',
            'Item',
            'Nilai',
            'Item',
            'Nilai',
            'Item',
            'Nilai',
            'Item',
            'Nilai',
            'Item',
            'Nilai',
            'Item',
            'Nilai'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:AG1')->applyFromArray([
            'font' => ['bold' => true],
            'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '1F2937']],
            'font' => ['color' => ['rgb' => 'FFFFFF']],
        ]);

        $sheet->getStyle('A:AG')->getAlignment()->setVertical(Alignment::VERTICAL_TOP);
        foreach (range('A', 'AG') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }

    public function title(): string
    {
        return 'Laporan Produksi Rotary';
    }
}
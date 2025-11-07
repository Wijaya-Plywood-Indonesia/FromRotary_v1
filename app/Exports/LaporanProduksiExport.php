<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class LaporanProduksiExport implements FromCollection, WithHeadings, WithStyles, WithTitle, WithEvents
{
    protected $dataProduksi;

    public function __construct($dataProduksi)
    {
        $this->dataProduksi = collect($dataProduksi)->groupBy('mesin');
    }

    public function collection()
    {
        $rows = collect();
        $rowIndex = 1;

        foreach ($this->dataProduksi as $mesinNama => $produksiList) {
            $first = $produksiList->first();
            $bahan = $first['bahan'] ?? [];
            $hasil = $first['hasil'] ?? [];
            $pekerja = $first['pekerja'] ?? [];
            $kendala = $first['kendala'] ?? 'Tidak ada kendala.';

            $hasilFlat = collect();
            foreach ($hasil as $key => $item) {
                if ($key === 'TOTAL_SEMUA_KW')
                    continue;
                $hasilFlat->push($item);
            }

            $maxRows = max(
                count($bahan),
                ceil($hasilFlat->count() / 2),
                count($pekerja),
                1
            );

            // Total per mesin
            $totalLembar = $produksiList->sum(fn($p) => $p['summary']['total_lembar'] ?? 0);
            $totalM3 = $produksiList->sum(fn($p) => $p['summary']['total_m3'] ?? 0);
            $totalJam = $produksiList->sum(fn($p) => $p['summary']['jam_kerja'] ?? 0);
            $totalTarget = $produksiList->sum(fn($p) => collect($p['pekerja'])->sum(fn($pe) => (float) str_replace('.', '', $pe['pot_target'] ?? 0)));
            $totalStatus = $totalLembar;
            $totalPekerja = $produksiList->sum(fn($p) => count($p['pekerja']));

            // === HEADER MESIN ===
            $rows->push(['MESIN: ' . strtoupper($mesinNama)]);
            $mesinRow = $rowIndex++;

            // === JUDUL ===
            $rows->push(['LAPORAN PRODUKSI ROTARY']);
            $titleRow = $rowIndex++;

            // === HEADER UTAMA ===
            $rows->push([
                'BAHAN',
                '',
                'HASIL PRODUKSI',
                '',
                '',
                '',
                '',
                '',
                '',
                'DATA PEKERJA',
                '',
                '',
                '',
                '',
                '',
                '',
                'KENDALA'
            ]);
            $mainHeaderRow = $rowIndex++;

            // === SUB HEADER ===
            $rows->push([
                'Lahan',
                'Batang',
                'Ukuran',
                'Kualitas',
                'Jenis',
                'Total',
                'm3',
                'Ukuran',
                'Kualitas',
                'Jenis',
                'Total',
                '',
                'ID',
                'Nama',
                'Masuk',
                'Pulang',
                'Ijin',
                'Target',
                'Ket'
            ]);
            $subHeaderRow = $rowIndex++;

            // === DATA ===
            for ($i = 0; $i < $maxRows; $i++) {
                $item1 = $hasilFlat->get($i * 2);
                $item2 = $hasilFlat->get($i * 2 + 1);
                $showM3_1 = $item1 && (!$item2 || empty($item2['ukuran']));
                $showM3_2 = $item2 && empty($item1['ukuran']);

                $rows->push([
                    $bahan[$i]['lahan'] ?? '',
                    $bahan[$i]['batang'] ?? '',
                    $item1['ukuran'] ?? '',
                    $item1['kw'] ?? '',
                    $item1['jenis_kayu'] ?? '',
                    $item1['lembar'] ?? '',
                    $showM3_1 ? number_format($item1['total_m3'] ?? 0, 3) : '',
                    $item2['ukuran'] ?? '',
                    $item2['kw'] ?? '',
                    $item2['jenis_kayu'] ?? '',
                    $item2['lembar'] ?? '',
                    $showM3_2 ? number_format($item2['total_m3'] ?? 0, 3) : '',
                    $pekerja[$i]['id'] ?? '',
                    $pekerja[$i]['nama'] ?? '',
                    $pekerja[$i]['jam_masuk'] ?? '',
                    $pekerja[$i]['jam_pulang'] ?? '',
                    $pekerja[$i]['ijin'] ?? '',
                    $pekerja[$i]['pot_target'] ?? '',
                    $i === 0 ? $kendala : ''
                ]);
            }
            $dataEndRow = $rowIndex + $maxRows - 1;

            // === TOTAL ===
            $totalStart = $rowIndex + $maxRows;
            $rows->push([
                'Total',
                '',
                '',
                '',
                '',
                number_format($totalLembar),
                number_format($totalM3, 4),
                $totalJam,
                number_format($totalTarget),
                number_format($totalStatus),
                '-',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                'Pekerja',
                $totalPekerja
            ]);
            $totalRow = $totalStart;
            $rowIndex = $totalStart + 2;

            // Spasi antar mesin
            $rows->push([]);
            $rowIndex++;
        }

        return $rows;
    }

    public function headings(): array
    {
        return [];
    }

    public function styles(Worksheet $sheet)
    {
        foreach (range('A', 'S') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $row = 1;

                foreach ($this->dataProduksi as $mesinNama => $produksiList) {
                    $first = $produksiList->first();
                    $maxRows = max(
                        count($first['bahan'] ?? []),
                        ceil(collect($first['hasil'] ?? [])->filter(fn($k) => $k !== 'TOTAL_SEMUA_KW')->count() / 2),
                        count($first['pekerja'] ?? []),
                        1
                    );

                    // === MESIN HEADER ===
                    $sheet->mergeCells("A{$row}:S{$row}");
                    $sheet->getStyle("A{$row}:S{$row}")->applyFromArray([
                        'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1F2937']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    ]);
                    $row++;

                    // === JUDUL ===
                    $sheet->mergeCells("A{$row}:S{$row}");
                    $sheet->getStyle("A{$row}:S{$row}")->applyFromArray([
                        'font' => ['bold' => true, 'size' => 14],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    ]);
                    $row++;

                    // === HEADER UTAMA ===
                    $sheet->mergeCells("A{$row}:B{$row}"); // BAHAN
                    $sheet->mergeCells("C{$row}:K{$row}"); // HASIL
                    $sheet->mergeCells("L{$row}:R{$row}"); // PEKERJA
                    $sheet->getStyle("A{$row}:S{$row}")->applyFromArray([
                        'font' => ['bold' => true],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E5E7EB']],
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    ]);
                    $row++;

                    // === SUB HEADER ===
                    $sheet->getStyle("A{$row}:S{$row}")->applyFromArray([
                        'font' => ['bold' => true],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'D1D5DB']],
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    ]);
                    $row++;

                    // === DATA ROWS ===
                    for ($i = 0; $i < $maxRows; $i++) {
                        $dataRow = $row + $i;
                        $sheet->getStyle("A{$dataRow}:S{$dataRow}")->applyFromArray([
                            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                            'alignment' => ['vertical' => Alignment::VERTICAL_TOP],
                        ]);
                        if ($i % 2 === 1) {
                            $sheet->getStyle("A{$dataRow}:S{$dataRow}")->getFill()
                                ->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F9FAFB');
                        }
                    }

                    // === KENDALA SPAN ===
                    $kendalaRow = $row;
                    $sheet->mergeCells("T{$kendalaRow}:T" . ($row + $maxRows - 1));

                    // === TOTAL ===
                    $totalStart = $row + $maxRows;
                    $sheet->mergeCells("A{$totalStart}:B{$totalStart}");
                    $sheet->getStyle("A{$totalStart}:K{$totalStart}")->applyFromArray([
                        'font' => ['bold' => true],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'DBEAFE']],
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    ]);

                    // Status (hijau soft)
                    $sheet->getStyle("J{$totalStart}")->applyFromArray([
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'D1FAE5']],
                        'font' => ['bold' => true, 'color' => ['rgb' => '065F46']],
                    ]);

                    // Pekerja (kuning soft)
                    $sheet->mergeCells("S{$totalStart}:T{$totalStart}");
                    $sheet->getStyle("S{$totalStart}:T{$totalStart}")->applyFromArray([
                        'font' => ['bold' => true],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FEF3C7']],
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    ]);

                    $row = $totalStart + 2;
                }
            },
        ];
    }

    public function title(): string
    {
        return 'Laporan Produksi Rotary';
    }
}
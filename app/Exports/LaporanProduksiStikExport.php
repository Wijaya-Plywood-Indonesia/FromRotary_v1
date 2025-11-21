<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LaporanProduksiStikExport implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize, WithStyles
{
    protected $dataStik;
    // PERUBAHAN UTAMA: Variabel ini melacak baris-baris penting untuk styling (header, total) di setiap set data.
    protected $styleRanges = []; 

    public function __construct(array $dataStik)
    {
        // Data Stik sekarang diasumsikan berisi array dari SEMUA entri produksi harian
        $this->dataStik = collect($dataStik);
    }

    public function collection(): Collection
    {
        $rows = collect();

        // PERUBAHAN UTAMA: Menghapus asumsi entri tunggal, langsung cek koleksi.
        if ($this->dataStik->isEmpty()) {
            return $rows; // Kembalikan koleksi kosong jika tidak ada data
        }

        // --- DATA HEADER UTAMA (SATU KALI) ---
        $rows->push(['LAPORAN PRODUKSI STIK']);
        // Mengambil tanggal dari entri pertama
        $rows->push(['Tanggal:', $this->dataStik->first()['tanggal'] ?? '']); 
        $rows->push([]); 
        
        $this->styleRanges['header_utama'] = ['A1:G1'];
        // PERUBAHAN UTAMA: Mulai pelacakan baris dari baris ke-4.
        $startRow = 4; 

        // PERUBAHAN UTAMA: LOOP UTAMA - Memproses setiap entri produksi
        $index = 0;
        foreach ($this->dataStik as $produksi) {
            
            $pekerja = $produksi['pekerja'] ?? [];
            $kendala = $produksi['kendala'] ?? 'Tidak ada kendala.';
            $kode_ukuran = $produksi['kode_ukuran'] ?? 'STIK';
            $target = $produksi['target_harian'] ?? 0;
            $jamKerja = $produksi['jam_kerja'] ?? 0;
            $hasil = $produksi['hasil_harian'] ?? 0;
            $selisih = $produksi['selisih'] ?? 0;
            $totalPekerja = count($pekerja);
            $totalPotonganHarian = $produksi['summary']['total_potongan'] ?? 0; 
            
            $selisihTampil = $selisih * -1;
            
            // --- JUDUL SEKSI PRODUKSI ---
            $rows->push(['PRODUKSI STIK - Entri ke-' . ($index + 1)]);
            // PERUBAHAN UTAMA: Merekam baris judul produksi ke styleRanges
            $this->styleRanges['judul_produksi'][] = "A{$startRow}:G{$startRow}"; 
            $startRow++;

            // --- RINGKASAN HARIAN ---
            $ringkasanStart = $startRow;
            $rows->push(['RINGKASAN HARIAN']); 
            $rows->push(['Target Harian:', (int) $target]);
            $rows->push(['Jam Kerja:', (int) $jamKerja]);
            $rows->push(['Total Hasil:', (int) $hasil]);
            $rows->push(['Selisih (vs Target):', (int) $selisihTampil]); 
            $rows->push(['Total Pekerja:', $totalPekerja . ' orang']);
            $rows->push(['Kendala:', $kendala]);
            $ringkasanEnd = $startRow + 6;
            $this->styleRanges['ringkasan_data'][] = [$ringkasanStart, $ringkasanEnd]; 
            $rows->push([]); // Baris kosong setelah ringkasan
            $startRow += 8; // Bertambah 8 baris (7 data + 1 kosong)
            
            // --- HEADER TABEL PEKERJA ---
            $headerRow = $startRow;
            $rows->push([
                'ID',
                'Nama',
                'Masuk',
                'Pulang',
                'Ijin',
                'Potongan Target (Rp)', 
                'Keterangan',
            ]);
            // PERUBAHAN UTAMA: Merekam baris header tabel
            $this->styleRanges['header_tabel'][] = "A{$headerRow}:G{$headerRow}"; 
            $startRow++;

            // --- DATA PEKERJA ---
            $dataPekerjaStart = $startRow;
            $totalPotonganDiTabel = 0;
            foreach ($pekerja as $p) {
                $potTargetFormatted = $p['pot_target'] ?? '0';
                // Konversi kembali ke integer/numerik untuk Excel
                $potTargetRaw = (int) str_replace(['.', 'Rp ', '-'], '', $potTargetFormatted);
                
                $rows->push([
                    $p['id'] ?? '-',
                    $p['nama'] ?? '-',
                    $p['jam_masuk'] ?? '-',
                    $p['jam_pulang'] ?? '-',
                    $p['ijin'] ?? '-',
                    $potTargetRaw > 0 ? (int) $potTargetRaw : 0, 
                    $p['keterangan'] ?? '-',
                ]);
                $startRow++; // Baris bertambah untuk setiap pekerja
            }
            $dataPekerjaEnd = $startRow - 1;
            $this->styleRanges['data_potongan'][] = ["F{$dataPekerjaStart}:F{$dataPekerjaEnd}"];

            // --- TOTAL PEKERJA ---
            $totalRow = $startRow;
            $rows->push([
                'TOTAL',
                '',
                '',
                '',
                '',
                (int) $totalPotonganHarian, // Menggunakan total dari summary
                '',
            ]);
            // PERUBAHAN UTAMA: Merekam baris total
            $this->styleRanges['total_row'][] = "A{$totalRow}:G{$totalRow}"; 
            $startRow++;

            // Spasi antara dua set produksi
            $rows->push([]);
            $rows->push([]);
            $startRow += 2; // Baris bertambah
            
            $index++;
        }

        return $rows;
    }

    public function headings(): array
    {
        return []; 
    }

    public function title(): string
    {
        return 'Laporan Produksi Stik';
    }

    // PERUBAHAN UTAMA: Fungsi styling ini DITULIS ULANG untuk menggunakan $this->styleRanges
    public function styles(Worksheet $sheet)
    {
        // 1. Header Utama (Hanya berlaku untuk A1)
        if (isset($this->styleRanges['header_utama'][0])) {
            $sheet->mergeCells($this->styleRanges['header_utama'][0]);
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
            $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        }
        
        $style = $sheet->getStyle('A:G');
        $style->getAlignment()->setWrapText(true);

        // 2. Loop Styling untuk Setiap Set Produksi
        $index = 0;
        foreach ($this->dataStik as $produksi) {
            
            // 2.1 Judul Produksi 
            $judulRange = $this->styleRanges['judul_produksi'][$index] ?? null;
            if ($judulRange) {
                $sheet->mergeCells($judulRange);
                $sheet->getStyle($judulRange)->getFont()->setBold(true)->setSize(12)->setUnderline(true);
            }

            // 2.2 Data Ringkasan (Label tebal)
            $ringkasanStart = $this->styleRanges['ringkasan_data'][$index][0] ?? null;
            $ringkasanEnd = $this->styleRanges['ringkasan_data'][$index][1] ?? null;

            if ($ringkasanStart && $ringkasanEnd) {
                // Style 'RINGKASAN HARIAN' (judul)
                $sheet->getStyle("A{$ringkasanStart}:G{$ringkasanStart}")->getFont()->setBold(true)->setSize(11);
                $sheet->mergeCells("A{$ringkasanStart}:G{$ringkasanStart}");

                // Style Label Ringkasan
                for ($i = $ringkasanStart + 1; $i <= $ringkasanEnd; $i++) {
                    $sheet->getStyle("A{$i}")->getFont()->setBold(true);
                }
            }
            
            // 2.3 Header Tabel Pekerja
            $headerRange = $this->styleRanges['header_tabel'][$index] ?? null;
            if ($headerRange) {
                $sheet->getStyle($headerRange)->getFont()->setBold(true)->setSize(11);
                $sheet->getStyle($headerRange)->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFA0A0A0'); 
            }

            // 2.4 Kolom Potongan Target (Format Angka)
            $potonganRange = $this->styleRanges['data_potongan'][$index][0] ?? null;
            if ($potonganRange) {
                $sheet->getStyle($potonganRange)->getNumberFormat()
                    ->setFormatCode('#,##0'); 
            }

            // 2.5 Baris Total
            $totalRange = $this->styleRanges['total_row'][$index] ?? null;
            if ($totalRange) {
                $sheet->getStyle($totalRange)->getFont()->setBold(true);
                $sheet->getStyle($totalRange)->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFD9D9D9'); 
                // Format kolom F di baris total
                 $sheet->getStyle("F" . substr($totalRange, 1))->getNumberFormat()
                    ->setFormatCode('#,##0');
            }
            
            $index++;
        }
    }
}
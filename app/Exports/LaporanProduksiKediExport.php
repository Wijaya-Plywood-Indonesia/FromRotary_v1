<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

// Menggunakan Maatwebsite\Excel\Facades\Excel; di kelas yang memanggilnya (LaporanKedi.php)
class LaporanProduksiKediExport implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize
{
    protected $dataKedi;

    public function __construct(array $dataKedi)
    {
        // Mengkonversi array dataKedi menjadi Collection
        $this->dataKedi = collect($dataKedi);
    }

    public function collection(): Collection
    {
        $rows = collect();

        if ($this->dataKedi->isEmpty()) {
            return $rows;
        }

        $tanggalLaporan = $this->dataKedi->first()['tanggal_produksi'] ?? 'N/A';
        
        // --- HEADER UTAMA LAPORAN ---
        $rows->push(['LAPORAN PRODUKSI KEDI']);
        $rows->push(['Tanggal Produksi:', $tanggalLaporan]);
        $rows->push([]);
        
        // --- LOOP UTAMA - Memproses setiap entri produksi kedi ---
        $index = 0;
        foreach ($this->dataKedi as $produksi) {
            $index++;
            
            // --- JUDUL SEKSI PRODUKSI ---
            // $rows->push(['PRODUKSI KEDI - Entri ke-' . $index]);
            // $rows->push(['ID Produksi:', $produksi['id_produksi']]);
            $rows->push(['Kode Kedi:', $produksi['kode_kedi'] ?? '-']);
            $rows->push(['Status Produksi:', strtoupper($produksi['status'])]);
            $rows->push(['Status Validasi:', $produksi['validasi_terakhir'] ?? 'Belum Ada']);
            $rows->push(['Validator:', $produksi['validasi_oleh'] ?? '-']);
            $rows->push([]); // Baris kosong setelah ringkasan

            // =========================================================================
            // 1. DETAIL MASUK KEDI
            // =========================================================================
            $rows->push(['DETAIL MASUK KEDI:']);
            
            if (!empty($produksi['detail_masuk'])) {
                // Header Detail Masuk
                $rows->push([
                    'No. Palet',
                    'Kode Kedi',
                    'Ukuran',
                    'Jenis Kayu',
                    'KW',
                    'Jumlah',
                    'Rencana Bongkar',
                ]);
                
                // Data Detail Masuk
                foreach ($produksi['detail_masuk'] as $detail) {
                    $rows->push([
                        $detail['no_palet'] ?? '-',
                        $detail['kode_kedi'] ?? '-',
                        $detail['ukuran'] ?? '-',
                        $detail['jenis_kayu'] ?? '-',
                        $detail['kw'] ?? '-',
                        $detail['jumlah'] ?? 0,
                        $detail['rencana_bongkar'] ?? '-',
                    ]);
                }
            } else {
                // $rows->push(['Tidak ada detail masuk pada entri ini.']);
            }
            $rows->push([]); // Spacer

            // =========================================================================
            // 2. DETAIL BONGKAR KEDI
            // =========================================================================
            $rows->push(['DETAIL BONGKAR KEDI:']);
            
            if (!empty($produksi['detail_bongkar'])) {
                // Header Detail Bongkar
                $rows->push([
                    'No. Palet',
                    'Kode Kedi',
                    'Ukuran',
                    'Jenis Kayu',
                    'KW',
                    'Jumlah',
                    // 'Tanggal Bongkar',
                ]);
                
                // Data Detail Bongkar
                foreach ($produksi['detail_bongkar'] as $detail) {
                    $rows->push([
                        $detail['no_palet'] ?? '-',
                        $detail['kode_kedi'] ?? '-',
                        $detail['ukuran'] ?? '-',
                        $detail['jenis_kayu'] ?? '-',
                        $detail['kw'] ?? '-',
                        $detail['jumlah'] ?? 0,
                        // $detail['tanggal_bongkar'] ?? '-',
                    ]);
                }
            } else {
                // $rows->push(['Tidak ada detail bongkar pada entri ini.']);
            }
            
            // --- TAMBAHAN JARAK antar entri produksi ---
            $rows->push([]);
            $rows->push([]);
        }

        return $rows;
    }

    public function headings(): array
    {
        // Karena kita menggunakan custom header di dalam collection(), kita return array kosong
        return []; 
    }

    public function title(): string
    {
        return 'Laporan Produksi Kedi';
    }
}
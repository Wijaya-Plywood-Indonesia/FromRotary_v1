<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
// PERUBAHAN UTAMA: Menghapus WithStyles karena styling tidak diperlukan
// use Maatwebsite\Excel\Concerns\WithStyles; 
// use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

// PERUBAHAN UTAMA: Menghapus implementasi WithStyles
class LaporanProduksiStikExport implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize
{
    protected $dataStik;
    // PERUBAHAN UTAMA: Variabel styleRanges tidak lagi digunakan
    // protected $styleRanges = []; 

    public function __construct(array $dataStik)
    {
        $this->dataStik = collect($dataStik);
    }

    public function collection(): Collection
    {
        $rows = collect();

        if ($this->dataStik->isEmpty()) {
            return $rows;
        }

        // --- DATA HEADER UTAMA (SATU KALI) ---
        $rows->push(['LAPORAN PRODUKSI STIK']);
        $rows->push(['Tanggal:', $this->dataStik->first()['tanggal'] ?? '']); 
        $rows->push([]); 
        
        // PERUBAHAN UTAMA: Tidak ada pelacakan baris untuk styling
        
        // --- LOOP UTAMA - Memproses setiap entri produksi ---
        $index = 0;
        foreach ($this->dataStik as $produksi) {
            
            $pekerja = $produksi['pekerja'] ?? [];
            $kendala = $produksi['kendala'] ?? 'Tidak ada kendala.';
            $target = $produksi['target_harian'] ?? 0;
            $jamKerja = $produksi['jam_kerja'] ?? 0;
            $hasil = $produksi['hasil_harian'] ?? 0;
            $selisih = $produksi['selisih'] ?? 0;
            $totalPekerja = count($pekerja);
            // $totalPotonganHarian tidak lagi digunakan karena baris TOTAL dihapus

            $selisihTampil = $selisih * -1;
            
            // --- JUDUL SEKSI PRODUKSI ---
            $rows->push(['PRODUKSI STIK - Entri ke-' . ($index + 1)]);
            
            // --- RINGKASAN HARIAN ---
            $rows->push(['RINGKASAN HARIAN']); 
            $rows->push(['Target Harian:', (int) $target]);
            $rows->push(['Jam Kerja:', (int) $jamKerja]);
            $rows->push(['Total Hasil:', (int) $hasil]);
            $rows->push(['Selisih (vs Target):', (int) $selisihTampil]); 
            $rows->push(['Total Pekerja:', $totalPekerja . ' orang']);
            $rows->push(['Kendala:', $kendala]);
            $rows->push([]); // Baris kosong setelah ringkasan
            
            // --- HEADER TABEL PEKERJA ---
            $rows->push([
                'ID',
                'Nama',
                'Masuk',
                'Pulang',
                'Ijin',
                'Potongan Target (Rp)', 
                'Keterangan',
            ]);

            // --- DATA PEKERJA ---
            foreach ($pekerja as $p) {
                $potTargetFormatted = $p['pot_target'] ?? '0';
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
            }
            
            // PERUBAHAN UTAMA: Baris TOTAL dihapus
            
            // --- TAMBAHAN JARAK ---
            // Menambahkan dua baris kosong sebagai pemisah (spacer) antar entri produksi
            $rows->push([]); 
            $rows->push([]);
            
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

    // PERUBAHAN UTAMA: Fungsi styles() dihapus total
    /*
    public function styles(Worksheet $sheet)
    {
        // Fungsi ini kosong karena styling tidak digunakan
    }
    */
}
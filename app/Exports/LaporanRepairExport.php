<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class LaporanRepairExport implements FromCollection, WithHeadings, WithTitle
{
    protected Collection $data;

    public function __construct(array $dataProduksi)
    {
        // GROUPING: MEJA + KODE UKURAN
        $this->data = collect($dataProduksi)
            ->groupBy(fn($item) => $item['nomor_meja'] . '|' . $item['kode_ukuran']);
    }

    public function collection()
    {
        $rows = collect();

        foreach ($this->data as $groupKey => $items) {

            $first = $items->first();

            $nomorMeja = $first['nomor_meja'];
            $ukuran = $first['ukuran'];
            $jenisKayu = $first['jenis_kayu'];
            $kw = $first['kw'];
            $tanggal = $first['tanggal'];

            $target = (int) $first['target'];
            $jamKerja = (int) $first['jam_kerja'];
            $hasil = (int) $first['hasil'];
            $selisih = (int) $first['selisih'];

            $targetPerJam = $jamKerja > 0
                ? round($target / $jamKerja, 2)
                : 0;

            $pekerja = $first['pekerja'] ?? [];

            // =============================
            // HEADER INFORMASI
            // =============================
            $rows->push(['MEJA', $nomorMeja]);
            $rows->push(['UKURAN', $ukuran]);
            $rows->push(['JENIS KAYU', $jenisKayu]);
            $rows->push(['KW', $kw]);
            $rows->push(['TANGGAL', $tanggal]);
            $rows->push([]);

            // =============================
            // HEADER TABEL
            // =============================
            $rows->push([
                'ID',
                'Nama',
                'Masuk',
                'Pulang',
                'Ijin',
                'Potongan Target',
                'Keterangan',
                '',
                'Target Harian',
                'Jam Kerja',
                'Target / Jam',
                'Hasil',
                'Selisih',
            ]);

            // =============================
            // DATA PEKERJA
            // =============================
            foreach ($pekerja as $p) {
                $potTarget = (int) ($p['pot_target'] ?? 0);

                $rows->push([
                    $p['id'] ?? '-',
                    $p['nama'] ?? '-',
                    $p['jam_masuk'] ?? '-',
                    $p['jam_pulang'] ?? '-',
                    $p['ijin'] ?? '-',
                    $potTarget > 0 ? $potTarget : '-',
                    $p['keterangan'] ?? '-',
                    '',
                    $target,
                    $jamKerja,
                    $targetPerJam,
                    $hasil,
                    $selisih >= 0 ? '+' . $selisih : $selisih,
                ]);
            }

            // =============================
            // TOTAL
            // =============================
            $totalPekerja = count($pekerja);
            $totalPotongan = collect($pekerja)->sum('pot_target');

            $rows->push([
                'TOTAL',
                '',
                '',
                '',
                '',
                $totalPotongan,
                '',
                '',
                $target,
                $jamKerja,
                $targetPerJam,
                $hasil,
                $selisih >= 0 ? '+' . $selisih : $selisih,
            ]);

            // SPASI ANTAR BLOK
            $rows->push([]);
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
        return 'Laporan Repair';
    }
}

<?php

namespace App\Filament\Pages\LaporanProduksi\Queries;

use App\Models\ProduksiRotary;

class LoadProduksi
{
    public static function run(string $tgl)
    {
        $start = $tgl . ' 00:00:00';
        $end = $tgl . ' 23:59:59';

        return ProduksiRotary::with([
            'mesin:id,nama_mesin',
            'detailPegawaiRotary.pegawai:id,kode_pegawai,nama_pegawai',
            'detailPaletRotary:id,id_produksi,id_ukuran,total_lembar',
        ])
            ->whereBetween('tgl_produksi', [$start, $end])
            ->get();
    }
}

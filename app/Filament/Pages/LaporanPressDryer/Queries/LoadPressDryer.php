<?php

namespace App\Filament\Pages\LaporanPressDryer\Queries;

use App\Models\ProduksiPressDryer;

class LoadPressDryer
{
    public static function run(string $tanggal)
    {
        // contoh query
        $start = $tanggal . ' 00:00:00';
        $end = $tanggal . ' 23:59:59';

        // Return ProduksiPressDryer
        return ProduksiPressDryer::with([
            'detailPegawais.pegawai:id,kode_pegawai,nama_pegawai',
            'detailHasils:id,nomor_palet,isi',
            'detailMesin.mesin:id,nama_mesin',
            'detailMesin.kategoriMesin:id,nama_kategori_mesin',
        ])
            ->select('id', 'tanggal_produksi', 'shift')
            ->whereBetween('tanggal_produksi', [$start, $end])
            ->orderBy('shift', 'asc')
            ->get();
    }
}
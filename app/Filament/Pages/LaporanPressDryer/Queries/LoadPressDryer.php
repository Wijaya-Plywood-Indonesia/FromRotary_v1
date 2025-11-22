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
            // Untuk Hasil 
            'detailHasils:id,id_produksi_dryer,no_palet,isi,id_ukuran',

            // Untuk Detail Masuk
            'detailMasuks:id,id_produksi_dryer',

            // Untuk Mesin
            'detailMesins:id,id_produksi_dryer,id_mesin_dryer',
            'detailMesins.mesin:id,nama_mesin',
            'detailMesins.kategoriMesin:id,nama_kategori_mesin',
        ])
            ->select('id', 'tanggal_produksi', 'shift')
            ->whereBetween('tanggal_produksi', [$start, $end])
            ->orderBy('shift', 'asc')
            ->get();
    }
}
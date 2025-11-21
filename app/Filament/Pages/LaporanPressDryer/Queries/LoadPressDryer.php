<?php

namespace App\Filament\Pages\LaporanPressDryer\Queries;

use App\Models\ProduksiPressDryer;

class LoadPressDryer
{
    public static function run(string $tanggal)
    {
        $start = $tanggal . ' 00:00:00';
        $end = $tanggal . ' 23:59:59';

        return ProduksiPressDryer::with([
            // Muat relasi pegawai (sesuaikan field dengan struktur tabel Anda)
            'detailPegawais' => function ($query) {
                $query->select('id', 'id_produksi_dryer', 'id_pegawai', 'masuk', 'pulang', 'ijin');
            },
            'detailPegawais.pegawai:id,kode_pegawai,nama_pegawai',

            // Muat relasi hasil dengan id_ukuran
            'detailHasils' => function ($query) {
                $query->select('id', 'id_produksi_dryer', 'no_palet', 'isi', 'id_ukuran');
            },

            // Muat relasi mesin
            'detailMesins' => function ($query) {
                $query->select('id', 'id_produksi_dryer', 'id_mesin', 'id_kategori_mesin');
            },
            'detailMesins.mesin:id,nama_mesin',
            'detailMesins.kategoriMesin:id,nama_kategori_mesin',
        ])
            ->select('id', 'tanggal_produksi', 'shift', 'kendala')
            ->whereBetween('tanggal_produksi', [$start, $end])
            ->orderBy('shift', 'asc')
            ->get();
    }
}
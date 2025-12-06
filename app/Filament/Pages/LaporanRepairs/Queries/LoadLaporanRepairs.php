<?php

namespace App\Filament\Pages\LaporanRepairs\Queries;

use App\Models\ProduksiRepair;

class LoadLaporanRepairs
{
    public static function run(string $tgl)
    {
        return ProduksiRepair::with([
            'modalRepairs.ukuran',
            'modalRepairs.jenisKayu',

            // Ambil semua pekerja hari itu
            'rencanaPegawais.pegawai',

            // Ambil semua hasil per pekerja
            'rencanaPegawais.rencanaRepairs.hasilRepairs',
        ])
            ->whereDate('tanggal', $tgl)
            ->get();
    }
}

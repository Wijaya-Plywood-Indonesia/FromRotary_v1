<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProduksiStik extends Model
{
    protected $table = 'produksi_stik';

    protected $fillable = [
        'tanggal_produksi',
        'kendala',
    ];

    public function detailPegawai()
    {
        return $this->hasMany(DetailPegawaiStik::class, 'id_produksi_stik');
    }

    public function detailMasuk()
    {
        return $this->hasMany(DetailMasukStik::class, 'id_produksi_stik');
    }

    public function detailHasil()
    {
        return $this->hasMany(DetailHasilStik::class, 'id_produksi_stik');
    }

    public function validasi()
    {
        return $this->hasMany(ValidasiStik::class, 'id_produksi_stik');
    }
}

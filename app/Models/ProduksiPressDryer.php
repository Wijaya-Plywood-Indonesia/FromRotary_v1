<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProduksiPressDryer extends Model
{
    protected $table = 'produksi_press_dryers';

    protected $fillable = [
        'tanggal_produksi',
        'shift',
        'kendala',
    ];


    public function detailMasuks()
    {
        return $this->hasMany(DetailMasuk::class, 'id_produksi_dryer');
    }

    public function detailHasils()
    {
        return $this->hasMany(DetailHasil::class, 'id_produksi_dryer');
    }

    public function detailMesins()
    {
        return $this->hasMany(DetailMesin::class, 'id_produksi_dryer');
    }

    public function validasiPressDryers()
    {
        return $this->hasMany(ValidasiPressDryer::class, 'id_produksi_dryer');
    }

    public function detailPegawais()
    {
        return $this->hasMany(DetailPegawai::class, 'id_produksi_dryer');
    }
}

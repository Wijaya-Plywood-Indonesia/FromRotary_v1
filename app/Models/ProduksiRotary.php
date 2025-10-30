<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProduksiRotary extends Model
{
    //
    protected $fillable = [
        'id_mesin',
        'tgl_produksi',
        'kendala',
    ];

    public function mesin()
    {
        return $this->belongsTo(Mesin::class, 'id_mesin');
    }
    public function produksi_rotaries()
    {
        return $this->hasMany(ProduksiRotary::class, 'id_produksi');
    }
    public function detailPegawaiRotary()
    {
        return $this->hasMany(PegawaiRotary::class, 'id_produksi');
    }
    public function detailLahanRotary()
    {
        return $this->hasMany(PenggunaanLahanRotary::class, 'id_produksi');
    }
    public function detailValidasiHasilRotary()
    {
        return $this->hasMany(ValidasiHasilRotary::class, 'id_produksi');
    }
    public function detailGantiPisauRotary()
    {
        return $this->hasMany(GantiPisauRotary::class, 'id_produksi');
    }
    public function detailPaletRotary()
    {
        return $this->hasMany(DetailHasilPaletRotary::class, 'id_produksi');
    }
    public function detailKayuPecah()
    {
        return $this->hasMany(KayuPecahRotary::class, 'id_produksi');
    }

}

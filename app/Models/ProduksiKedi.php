<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProduksiKedi extends Model
{
    protected $table = 'produksi_kedi';

    protected $fillable = [
        'tanggal',
        'kendala',
        'status',
    ];

    public function detailMasukKedi()
    {
        return $this->hasMany(DetailMasukKedi::class, 'id_produksi_kedi');
    }

    public function detailBongkarKedi()
    {
        return $this->hasMany(DetailBongkarKedi::class, 'id_produksi_kedi');
    }

    public function validasiKedi()
    {
        return $this->hasMany(ValidasiKedi::class, 'id_produksi_kedi');
    }
    public function validasiTerakhir()
    {
        return $this->hasOne(ValidasiKedi::class, 'id_produksi_kedi')->latestOfMany();
    }
}

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

    public function detailMasuk()
    {
        return $this->hasMany(DetailMasukKedi::class, 'id_produksi_kedi');
    }

    public function detailBongkar()
    {
        return $this->hasMany(DetailBongkarKedi::class, 'id_produksi_kedi');
    }

    public function validasi()
    {
        return $this->hasMany(ValidasiKedi::class, 'id_produksi_kedi');
    }
}

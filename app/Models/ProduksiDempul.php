<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProduksiDempul extends Model
{
    protected $table = 'produksi_dempuls';

    protected $fillable = [
        'tanggal',
        'kendala',
    ];

    public function rencanaPegawaiDempuls()
    {
        return $this->hasMany(RencanaPegawaiDempul::class, 'id_produksi_dempul');
    }

    public function detailDempuls()
    {
        return $this->hasMany(DetailDempul::class, 'id_produksi_dempul');
    }

    public function validasiDempuls()
    {
        return $this->hasMany(ValidasiDempul::class, 'id_produksi_dempul');
    }
}

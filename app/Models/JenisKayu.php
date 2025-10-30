<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisKayu extends Model
{

    protected $fillable = [
        'kode_kayu',
        'nama_kayu',
        'keterangan',
    ];
    public function detailPenggunaanJenisDiLahan()
    {
        return $this->hasMany(PenggunaanLahanRotary::class, 'id_jenis_kayu');
    }
    public function kayuMasuk()
    {
        return $this->hasMany(HargaKayu::class, 'id_jenis_kayu', 'id');
    }
}

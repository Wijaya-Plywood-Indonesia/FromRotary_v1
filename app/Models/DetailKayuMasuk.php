<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailKayuMasuk extends Model
{
    //
    protected $table = 'detail_kayu_masuks';

    protected $fillable = [
        'id_kayu_masuk',
        'diameter',
        'panjang',
        'grade',
        'jumlah_batang',
        'keterangan',
    ];

    /**
     * Relasi ke model KayuMasuk
     * (Setiap detail kayu masuk dimiliki oleh satu kayu masuk)
     */
    public function kayuMasuk()
    {
        return $this->belongsTo(KayuMasuk::class, 'id_kayu_masuk');
    }

}

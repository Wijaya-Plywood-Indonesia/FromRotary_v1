<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TurunKayu extends Model
{
    //

    protected $table = 'turun_kayus';
    protected $primaryKey = 'id';

    // Sesuaikan fillable dengan ERD Anda, bukan hanya id dan tanggal
    protected $fillable = [
        'tanggal',
        'id_kayu_masuk',
    ];

    public function kayuMasuk()
    {
        return $this->belongsTo(KayuMasuk::class, 'id_kayu_masuk');
    }
    public function detailTurunKayu()
    {
        return $this->hasMany(DetailTurunKayu::class, 'id_turun_kayu');
    }
}

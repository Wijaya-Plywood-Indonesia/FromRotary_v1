<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailTurunKayu extends Model
{
    protected $primaryKey = 'id';
    //
    //
    protected $fillable = [
        'id_turun_kayu',
        'id_pegawai',
        'id_kayu_masuk'
    ];
    protected $casts = [
        'id_turun_kayus' => 'integer',
        'id_pegawai' => 'integer',
    ];

    public function turunKayu()
    {
        return $this->belongsTo(TurunKayu::class, 'id_turun_kayu');
    }

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'id_pegawai');
    }

    public function kayuMasuk(): BelongsTo
    {
        return $this->belongsTo(KayuMasuk::class, 'id_kayu_masuk');
    }
}

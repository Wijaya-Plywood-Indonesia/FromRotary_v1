<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailTurunKayu extends Model
{
    protected $primaryKey = 'id';
    //
    //
    protected $fillable = [
        'id_turun_kayu',
        'id_pegawai',
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
}

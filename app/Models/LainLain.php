<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LainLain extends Model
{
    protected $table = 'lain_lain';

    protected $fillable = [
        'id_pegawai',
        'masuk',
        'pulang',
        'ijin',
        'ket',
        'hasil',
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'id_pegawai');
    }
}

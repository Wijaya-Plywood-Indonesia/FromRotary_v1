<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PegawaiRotary extends Model
{
    //isian
    protected $fillable = [
        'id_produksi',
        'id_pegawai',
        'role',
        'jam_masuk',
        'jam_pulang',
    ];
}

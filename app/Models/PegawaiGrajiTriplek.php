<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PegawaiGrajiTriplek extends Model
{
    protected $table = 'pegawai_graji_triplek';

    protected $fillable = [
        'id_produksi_hp',
        'id_pegawai',
        'tugas',
        'masuk',
        'pulang',
        'ijin',
        'ket',
        
    ];
}

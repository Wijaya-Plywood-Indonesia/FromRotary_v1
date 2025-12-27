<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PegawaiJoint extends Model
{
    protected $table = 'pegawai_joint';

    protected $fillable = [
        'id_produksi_joint',
        'id_pegawai',
        'tugas',
        'masuk',
        'pulang',
        'ijin',
        'ket',
    ];
}

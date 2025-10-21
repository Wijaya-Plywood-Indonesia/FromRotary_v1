<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pegawai extends Model
{

    // Kolom yang boleh diisi mass-assignment
    protected $fillable = [
        'kode_pegawai',
        'nama_pegawai',
        'alamat',
        'no_telepon_pegawai',
        'jenis_kelamin_pegawai',
        'tanggal_masuk',
        'foto',
    ];
}

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
}

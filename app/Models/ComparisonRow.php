<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComparisonRow extends Model
{
    //
    protected $table = null; // tanpa tabel
    public $timestamps = false;

    protected $fillable = [
        'id',
        'id_jenis_kayu',
        'id_lahan',
        'diameter',
        'panjang',
        'grade',
        'detail_jumlah',
        'turusan_jumlah',
        'selisih',
    ];
}

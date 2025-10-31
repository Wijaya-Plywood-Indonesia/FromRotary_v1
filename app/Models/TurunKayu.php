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
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GantiPisauRotary extends Model
{
    //
    protected $table = 'ganti_pisau_rotaries';
    protected $primaryKey = 'id';
    //
    protected $fillable = [

        'id_produksi',
        'jam_mulai_ganti_pisau',
        'jam_selesai_ganti',
    ];

}

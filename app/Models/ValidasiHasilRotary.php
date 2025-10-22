<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ValidasiHasilRotary extends Model
{
    //
    protected $table = 'detail_hasil_palet_rotaries';
    protected $primaryKey = 'id';
    //
    protected $fillable = [

        'id_produksi',
        'produksi_rotaries',
        'timestamp_laporan',
        'id_ukuran',
        'kw',
        'total_lembar',
    ];
}

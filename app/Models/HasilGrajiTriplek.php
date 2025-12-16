<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HasilGrajiTriplek extends Model
{
    protected $table = 'hasil_graji_triplek';

    protected $fillable = [
        'id_produksi_graji_triplek',
        'id_barang_setengah_jadi_hp',
        'no_palet',
        'isi',
    ];
}

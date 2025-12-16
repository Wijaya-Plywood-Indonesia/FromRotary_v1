<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProduksiGrajitriplek extends Model
{
    protected $table = 'produksi_graji_triplek';

    protected $fillable = [
        'id_produksi_graji_triplek',
        'tanggal_produksi',
        'status',
        'kendala',
    ];
}

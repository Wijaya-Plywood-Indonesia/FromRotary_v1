<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProduksiRotary extends Model
{
    //
    protected $fillable = [
        'id_mesin',
        'tgl_produksi',
        'kendala',
    ];

    public function mesin()
    {
        return $this->belongsTo(Mesin::class, 'id_mesin');
    }
}

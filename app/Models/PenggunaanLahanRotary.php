<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenggunaanLahanRotary extends Model
{
    protected $table = 'penggunaan_lahan_rotaries';
    protected $primaryKey = 'id';
    //
    protected $fillable = [
        'id_lahan',
        'id_produksi',
        'jumlah_batang',

    ];
    public function produksi_rotary()
    {
        return $this->belongsTo(ProduksiRotary::class, 'id_produksi');
    }
    public function lahan()
    {
        return $this->belongsTo(Lahan::class, 'id_lahan', 'id');
    }

}

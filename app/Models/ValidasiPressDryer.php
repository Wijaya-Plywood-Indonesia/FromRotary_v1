<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ValidasiPressDryer extends Model
{
    protected $table = 'validasis';

    protected $fillable = [
        'role',
        'status',
        'id_produksi_dryer',
    ];

    public function produksi()
    {
        return $this->belongsTo(ProduksiPressDryer::class, 'id_produksi_dryer');
    }
}

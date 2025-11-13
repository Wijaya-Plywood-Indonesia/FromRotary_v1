<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailMesin extends Model
{
    protected $table = 'detail_mesins';

    protected $fillable = [
        'id_mesin_dryer',
        'jam_kerja_mesin',
        'id_produksi_dryer',
    ];

    public function produksi()
    {
        return $this->belongsTo(ProduksiPressDryer::class, 'id_produksi_dryer');
    }
}

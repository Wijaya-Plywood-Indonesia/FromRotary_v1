<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailMasuk extends Model
{
    protected $table = 'detail_masuks';

    protected $fillable = [
        'no_palet',
        'kw',
        'isi',
        'ukuran',
        'jenis_kayu',
        'id_produksi_dryer',
    ];


    public function kayuMasuk()
    {
        return $this->belongsTo(KayuMasuk::class, 'id_kayu_masuk');
    }
    public function jenisKayu()
    {
        return $this->belongsTo(JenisKayu::class, 'id_jenis_kayu', 'id');
    }
    public function produksi()
    {
        return $this->belongsTo(ProduksiPressDryer::class, 'id_produksi_dryer');
    }
}

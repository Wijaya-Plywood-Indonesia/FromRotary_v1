<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailHasil extends Model
{
    protected $table = 'detail_hasil';

    protected $fillable = [
        'no_palet',
        'kw',
        'isi',
        'id_kayu_masuk',
        'id_jenis_kayu',
        'id_produksi_dryer',
    ];

    public function kayuMasuk()
    {
        return $this->belongsTo(KayuMasuk::class, 'id_kayu_masuk');
    }

    public function jenisKayu()
    {
        return $this->belongsTo(JenisKayu::class, 'id_jenis_kayu');
    }

    public function produksiDryer()
    {
        return $this->belongsTo(ProduksiPressDryer::class, 'id_produksi_dryer');
    }
}
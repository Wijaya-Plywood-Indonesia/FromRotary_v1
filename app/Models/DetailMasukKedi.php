<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailMasukKedi extends Model
{
    protected $table = 'detail_masuk_kedi';

    protected $fillable = [
        'no_palet',
        'id_ukuran',
        'id_jenis_kayu',
        'kw',
        'jumlah',
        'rencana_bongkar',
        'id_produksi_kedi',
    ];

    public function produksi()
    {
        return $this->belongsTo(ProduksiKedi::class, 'id_produksi_kedi');
    }

    public function jenisKayu()
    {
        return $this->belongsTo(JenisKayu::class, 'id_jenis_kayu', 'id');
    }

    public function ukuran()
    {
        return $this->belongsTo(Ukuran::class, 'id_ukuran');
    }
}

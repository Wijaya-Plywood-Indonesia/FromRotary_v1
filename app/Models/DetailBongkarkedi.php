<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailBongkarkedi extends Model
{
    protected $table = 'detail_bongkar_kedi';

    protected $fillable = [
        'no_palet',
        'id_jenis_kayu',
        'id_ukuran',
        'kw',
        'jumlah',
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

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TriplekHasilHp extends Model
{
    protected $table = 'triplek_hasil_hp';

    protected $fillable = [
        'id_produksi_hp',
        'no_palet',
        'id_jenis_kayu',
        'id_ukuran',
        'kw',
        'isi',
    ];

    public function produksiHp()
    {
        return $this->belongsTo(ProduksiHp::class, 'id_produksi_hp');
    }

    public function ukuran()
    {
        return $this->belongsTo(Ukuran::class, 'id_ukuran');
    }

    public function jenisKayu()
    {
        return $this->belongsTo(JenisKayu::class, 'id_jenis_kayu','id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HasilSanding extends Model
{
    //
    protected $table = 'hasil_sandings';

    protected $fillable = [
        'id_produksi_sanding',
        'id_barang_setengah_jadi',
        'kuantitas',
        'jumlah_sanding',
        'no_palet',
        'status',
    ];

    public function produksiSanding()
    {
        return $this->belongsTo(ProduksiSanding::class, 'id_produksi_sanding');
    }

    public function barangSetengahJadi()
    {
        return $this->belongsTo(BarangSetengahJadiHp::class, 'id_barang_setengah_jadi');
    }
}

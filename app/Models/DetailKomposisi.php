<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailKomposisi extends Model
{
    protected $table = 'detail_komposisi';
    protected $fillable = [
        'id_komposisi',
        'id_barang_setengah_jadi_hp',
        'lapisan',
        'keterangan',
    ];
    public function komposisi()
    {
        return $this->belongsTo(Komposisi::class, 'id_komposisi');
    }

    public function barangSetengahJadiHp()
    {
        return $this->belongsTo(BarangSetengahJadiHp::class, 'id_barang_setengah_jadi_hp');
    }
}

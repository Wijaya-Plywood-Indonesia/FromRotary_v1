<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BarangSetengahJadiHp extends Model
{
    protected $table = 'barang_setengah_jadi_hp';

    protected $fillable = [
        'id_ukuran',
        'grade',
        'keterangan',
    ];

    public function ukuran()
    {
        return $this->belongsTo(Ukuran::class, 'id_ukuran');
    }
}

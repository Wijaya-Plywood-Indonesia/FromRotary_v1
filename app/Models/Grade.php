<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    protected $table = 'grades';
    protected $fillable = [
        'nama_grade',
        'id_kategori_barang',
    ];

    public function kategoriBarang ()
    {
        return $this->belongsTo(KategoriBarang::class, 'id_kategori_barang');
    }

    public function barangSetengahJadiHp()
    {
        return $this->hasMany(BarangSetengahJadiHp::class, 'id_grade');
    }
}

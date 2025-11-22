<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotaBarangMasuk extends Model
{
    //
    protected $table = 'nota_barang_masuks';

    protected $fillable = [
        'tanggal',
        'no_nota',
        'tujuan_nota',
        'dibuat_oleh',
        'divalidasi_oleh',
    ];
    protected $casts = [
        'tanggal' => 'date:Y-m-d',
    ];
    /**
     * Relasi ke user pembuat.
     */
    public function dibuatOleh()
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }

    /**
     * Relasi ke user yang memvalidasi.
     */
    public function divalidasiOleh()
    {
        return $this->belongsTo(User::class, 'divalidasi_oleh');
    }

    /**
     * Relasi ke detail barang.
     */
    public function detail()
    {
        return $this->hasMany(DetailNotaBarangMasuk::class, 'id_nota_bm');
    }
}

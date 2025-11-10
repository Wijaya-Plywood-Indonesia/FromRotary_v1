<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailTurusanKayu extends Model
{
    use HasFactory;

    protected $table = 'detail_turusan_kayus';
    //
    protected $fillable = [
        'id_kayu_masuk',
        'nomer_urut',
        'lahan_id',
        'jenis_kayu_id',
        'panjang',
        'grade',
        'diameter',
        'kuantitas',
    ];
    public function kayuMasuk()
    {
        return $this->belongsTo(KayuMasuk::class, 'id_kayu_masuk');
    }

    /**
     * Relasi ke tabel lahans
     * DetailTurusanKayu milik satu Lahan
     */
    public function lahan()
    {
        return $this->belongsTo(Lahan::class, 'lahan_id');
    }

    /**
     * Relasi ke tabel jenis_kayus
     * DetailTurusanKayu milik satu JenisKayu
     */
    public function jenisKayu()
    {
        return $this->belongsTo(JenisKayu::class, 'jenis_kayu_id');
    }
}

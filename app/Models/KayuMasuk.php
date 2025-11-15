<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KayuMasuk extends Model
{
    //
    protected $table = 'kayu_masuks';

    protected $fillable = [
        'jenis_dokumen_angkut',
        'upload_dokumen_angkut',
        'tgl_kayu_masuk',
        'seri',
        'kubikasi',
        'id_supplier_kayus',
        'id_kendaraan_supplier_kayus',
        'id_dokumen_kayus',
    ];
    protected static function booted()
    {
        static::creating(function ($record) {
            $lastSeri = static::max('seri');

            if (!$lastSeri) {
                $record->seri = 1;
            } else {
                $record->seri = ($lastSeri >= 1000) ? 1 : $lastSeri + 1;
            }
        });
    }

    //==Relasi 
    public function penggunaanSupplier()
    {
        return $this->belongsTo(SupplierKayu::class, 'id_supplier_kayus');
    }
    public function penggunaanKendaraanSupplier()
    {
        return $this->belongsTo(KendaraanSupplierKayu::class, 'id_kendaraan_supplier_kayus');
    }
    public function penggunaanDokumenKayu()
    {
        return $this->belongsTo(DokumenKayu::class, 'id_dokumen_kayus');
    }

    public function tempatKayu(): HasMany
    {
        return $this->hasMany(TempatKayu::class, 'id_tempat_kayu');
    }

    public function detailMasukanKayu(): HasMany
    {
        return $this->hasMany(DetailKayuMasuk::class, 'id_kayu_masuk');
    }
    public function detailTurunKayu(): HasMany
    {
        return $this->hasMany(TurunKayu::class, 'id_kayu_masuk');
    }
    public function detailTurusanKayus()
    {
        return $this->hasMany(DetailTurusanKayu::class, 'id_kayu_masuk');
    }

    public function detailTurunKayus()
    {
        return $this->hasMany(DetailTurunKayu::class, 'id_kayu_masuk');
    }

    public function detailMasuk()
    {
        return $this->hasMany(DetailMasuk::class, 'id_kayu_masuk');
    }
    public function notakayu()
    {
        return $this->hasMany(DetailMasuk::class, 'id_kayu_masuk');
    }
}

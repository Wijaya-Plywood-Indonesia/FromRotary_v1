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
        'id_supplier_kayus',
        'id_kendaraan_supplier_kayus',
        'id_dokumen_kayus',
    ];

    /**
     * Atur agar field tgl_kayu_masuk dianggap sebagai tanggal.
     */


    /**
     * Event model: otomatis isi seri saat membuat data baru.
     */
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
}

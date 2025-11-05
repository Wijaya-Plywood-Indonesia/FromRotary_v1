<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;
class TempatKayu extends Model
{
    //

    protected $table = 'tempat_kayus';
    protected $primaryKey = 'id';

    protected $with = ['lahan', 'kayuMasuk.detailMasukanKayu'];

    protected $fillable = [
        'jumlah_batang',
        'poin',
        'id_kayu_masuk',
        'id_lahan',
        'id_turun_kayu'
    ];

    public function kayuMasuk(): BelongsTo
    {
        return $this->belongsTo(KayuMasuk::class, 'id_kayu_masuk');
    }

    public function riwayatKayu(): HasMany
    {
        return $this->hasMany(RiwayatKayu::class, 'id_tempat_kayu');
    }

    public function lahan(): BelongsTo
    {
        return $this->belongsTo(Lahan::class, 'id_lahan');
    }

    public function turunKayu(): BelongsTo
    {
        return $this->belongsTo(TurunKayu::class, 'id_turun_kayu');
    }

    // Detail kayu
    protected function detailKayu(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->kayuMasuk?->detailMasukanKayu->first()
        );
    }

    protected function diameter(): Attribute
    {
        // Ambil diameter dari helper 'detailKayu'
        return Attribute::make(
            get: fn() => $this->detailKayu?->diameter ?? 0
        );
    }

    protected function kubikasi(): Attribute
    {
        return Attribute::make(
            get: function () {
                $detail = $this->detailKayu; // Panggil helper
    
                // Cek data
                if (!$detail || !$this->jumlah_batang) {
                    return 0;
                }

                // Asumsi 'diameter' dalam CM dan 'panjang' dalam M
                $diameter_cm = $detail->diameter ?? 0;
                $panjang_m = $detail->panjang ?? 0; // Pastikan 'panjang' ada di 'detailTurunKayu'
                $jumlah_batang = $this->jumlah_batang;

                // --- Hitung Volume ---
                $diameter_m = $diameter_cm / 100;
                $volume_satu_batang = 0.7854 * pow($diameter_m, 2) * $panjang_m;
                $total_kubikasi = $volume_satu_batang * $jumlah_batang;

                return round($total_kubikasi, 2);
            }
        );
    }

    protected function selectLabel(): Attribute
    {
        return Attribute::make(
            get: function () {
                $kode_lahan = $this->lahan?->kode_lahan ?? '[Tanpa Lahan]';

                $kubikasi = $this->kubikasi; // Ini akan memanggil kubikasi()
    
                // Format label yang akan muncul di tabel dan dropdown
                return "{$kode_lahan} | {$this->jumlah_batang} btg | {$kubikasi} cm³";
            }
        );
    }

}

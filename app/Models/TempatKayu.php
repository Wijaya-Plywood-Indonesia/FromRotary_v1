<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TempatKayu extends Model
{
    //

    protected $table = 'tempat_kayus';
    protected $primaryKey = 'id';

    protected $with = ['lahan'];

    protected $fillable = [
        'jumlah_batang',
        'poin',
        'id_kayu_masuk',
        'id_lahan'
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

    public function getSelectLabelAttribute()
    {
        return "{$this->lahan->nama_lahan} | {$this->jumlah_batang} batang | {$this->poin} poin";

    }
}

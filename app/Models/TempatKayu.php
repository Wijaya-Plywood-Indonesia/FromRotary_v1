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

    protected $fillable = [
        'jumlah',
        'poin',
        'id_kayu_masuk'
    ];

    public function kayuMasuk(): BelongsTo
    {
        return $this->belongsTo(KayuMasuk::class, 'id_kayu_masuk');
    }

    public function riwayatKayu(): HasMany
    {
        return $this->hasMany(RiwayatKayu::class, 'id_riwayat_kayu');
    }
}

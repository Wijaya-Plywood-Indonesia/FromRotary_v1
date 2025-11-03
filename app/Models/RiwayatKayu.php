<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiwayatKayu extends Model
{
    //
    protected $table = 'riwayat_kayus';

    protected $primaryKey = 'id';

    protected $fillable = [
        'tanggal_masuk',
        'tanggal_digunakan',
        'tanggal_habis',
        'id_tempat_kayu'
    ];

    public function tempatKayu(): BelongsTo
    {
        return $this->belongsTo(TempatKayu::class, 'tempat_kayu_id');
    }
}

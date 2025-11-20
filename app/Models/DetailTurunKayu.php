<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailTurunKayu extends Model
{
    protected $fillable = [
        'id_turun_kayu',
        'id_kayu_masuk',
        'status',
        'foto',
        'nama_supir'
    ];

    public function turunKayu()
    {
        return $this->belongsTo(TurunKayu::class, 'id_turun_kayu');
    }

    public function kayuMasuk(): BelongsTo
    {
        return $this->belongsTo(KayuMasuk::class, 'id_kayu_masuk');
    }

    public function pegawaiTurunKayu()
    {
        return $this->hasMany(PegawaiTurunKayu::class, 'id_detail_turun_kayu');
    }

    // mendapatkan list nama pekerja dalam bentuk string
    public function getPegawaiListAttribute(): string
    {
        return $this->pegawaiTurunKayu
            ->map(fn($item) => $item->pegawai->nama_pegawai ?? '-')
            ->join(', ');
    }
}

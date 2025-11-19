<?php
// app/Models/BahanRepair.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BahanRepair extends Model
{
    protected $table = 'bahan_repairs';

    protected $fillable = [
        'id_repair',
        'id_ukuran',
        'id_jenis',
        'kw',
        'total_lembar',
    ];

    protected $with = ['ukuran', 'jenisKayu'];

    // Relasi ke Repair
    public function repair(): BelongsTo
    {
        return $this->belongsTo(Repair::class, 'id_repair');
    }

    // Relasi ke Ukuran
    public function ukuran(): BelongsTo
    {
        return $this->belongsTo(Ukuran::class, 'id_ukuran');
    }

    // Relasi ke Jenis Kayu
    public function jenisKayu(): BelongsTo
    {
        return $this->belongsTo(JenisKayu::class, 'id_jenis');
    }

    public function getUkuranLabelAttribute(): string
    {
        return $this->ukuran?->nama_ukuran ?? 'Tidak diketahui';
    }

    public function getJenisLabelAttribute(): string
    {
        return $this->jenisKayu?->kode_kayu
            ? $this->jenisKayu->kode_kayu . ' - ' . $this->jenisKayu->nama_kayu
            : $this->jenisKayu?->nama_kayu ?? 'Tidak diketahui';
    }
}
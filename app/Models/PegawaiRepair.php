<?php
// app/Models/PegawaiRepair.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PegawaiRepair extends Model
{
    protected $table = 'pegawai_repairs';

    protected $fillable = [
        'id_repair',
        'id_pegawai',
        'jam_masuk',
        'jam_pulang',
        'ijin',
        'keterangan',
        'nomor_meja',
    ];

    protected $casts = [
        'jam_masuk' => 'datetime:H:i',
        'jam_pulang' => 'datetime:H:i',
    ];

    public function repair()
    {
        return $this->belongsTo(Repair::class, 'id_repair');
    }

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'id_pegawai');
    }
}
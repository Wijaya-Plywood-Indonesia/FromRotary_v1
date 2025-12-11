<?php
// app/Models/Repair.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Repair extends Model
{
    protected $table = 'repairs';

    protected $fillable = [
        'tanggal',
        'jumlah_meja',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function pegawaiRepairs(): HasMany
    {
        return $this->hasMany(PegawaiRepair::class, 'id_repair');
    }

    public function bahanRepairs(): HasMany
    {
        return $this->hasMany(BahanRepair::class, 'id_repair');
    }

    public function validasiRepairs()
    {
        return $this->hasMany(ValidasiRepair::class, 'id_repair');
    }
}
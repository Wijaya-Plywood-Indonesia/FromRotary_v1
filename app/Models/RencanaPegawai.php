<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RencanaPegawai extends Model
{
    use HasFactory;

    protected $table = 'rencana_pegawais';

    protected $fillable = [
        'id_pegawai',
        'nomor_meja',
    ];

    /**
     * Relasi ke model Pegawai
     */
    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'id_pegawai');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RencanaRepair extends Model
{
    use HasFactory;

    protected $table = 'rencana_repairs';

    protected $fillable = [
        'id_produksi_repair',
        'id_rencana_pegawai',
        'id_ukuran',
        'id_jenis_kayu',
        'kw',
    ];

    protected $with = [
        'produksiRepair',
        'rencanaPegawai',
        'ukuran',
        'jenisKayu',
    ];

    // === RELASI ===

    /**
     * Relasi ke ProduksiRepair (hari produksi)
     */
    public function produksiRepair(): BelongsTo
    {
        return $this->belongsTo(ProduksiRepair::class, 'id_produksi_repair');
    }

    /**
     * Relasi ke Ukuran
     */
    public function ukuran(): BelongsTo
    {
        return $this->belongsTo(Ukuran::class, 'id_ukuran');
    }

    /**
     * Relasi ke Jenis Kayu
     */
    public function jenisKayu(): BelongsTo
    {
        return $this->belongsTo(JenisKayu::class, 'id_jenis_kayu');
    }

    public function rencanaPegawai()
    {
        return $this->belongsTo(RencanaPegawai::class, 'id_rencana_pegawai');
    }

    public function hasilRepairs()
    {
        return $this->hasMany(HasilRepair::class, 'id_rencana_repair');
    }
}
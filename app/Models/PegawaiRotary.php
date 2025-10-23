<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PegawaiRotary extends Model
{
    protected $table = 'pegawai_rotaries';
    protected $primaryKey = 'id';
    //isian
    protected $fillable = [
        'id_produksi',
        'id_pegawai',
        'role',
        'jam_masuk',
        'jam_pulang',
    ];
    public function produksi()
    {
        return $this->belongsTo(ProduksiRotary::class, 'id_produksi');
    }

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'id_pegawai');
    }

}

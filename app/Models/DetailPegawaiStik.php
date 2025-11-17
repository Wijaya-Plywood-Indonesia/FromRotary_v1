<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailPegawaiStik extends Model
{
    protected $table = 'detail_pegawai_stik';

    protected $fillable = [
        'tugas',
        'masuk',
        'pulang',
        'ijin',
        'ket',
        'id_produksi_stik',
        'id_pegawai',
    ];

    public function produksi()
    {
        return $this->belongsTo(ProduksiStik::class, 'id_produksi_stik');
    }

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'id_pegawai');
    }
}

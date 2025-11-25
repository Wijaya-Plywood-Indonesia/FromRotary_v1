<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailPegawaiHp extends Model
{
    protected $table = 'detail_pegawai_hp';

    protected $fillable = [
        'id_produksi_hp',
        'id_pegawai',
        'tugas',
        'masuk',
        'pulang',
        'ijin',
        'ket',
        
    ];

    public function produksiHp()
    {
        return $this->belongsTo(ProduksiHp::class, 'id_produksi_hp');
    }

    public function pegawaiHp()
    {
        return $this->belongsTo(Pegawai::class, 'id_pegawai');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PegawaiJoint extends Model
{
    protected $table = 'pegawai_joint';

    protected $fillable = [
        'id_produksi_joint',
        'id_pegawai',
        'tugas',
        'masuk',
        'pulang',
        'ijin',
        'ket',
    ];

    public function produksiJoint()
    {
        return $this->belongsTo(ProduksiJoint::class, 'id_produksi_joint');
    }

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'id_pegawai');
    }
}

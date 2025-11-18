<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pegawai extends Model
{
    protected $table = 'pegawais';
    protected $primaryKey = 'id';
    // Kolom yang boleh diisi mass-assignment
    protected $fillable = [
        'kode_pegawai',
        'nama_pegawai',
        'alamat',
        'no_telepon_pegawai',
        'jenis_kelamin_pegawai',
        'tanggal_masuk',
        'foto',
    ];
    public function pegawaiRotaries()
    {
        return $this->hasMany(PegawaiRotary::class, 'id_pegawai');
    }

    public function detailTurunKayu()
    {
        return $this->hasMany(DetailTurunKayu::class, 'id_pegawai');
    }

    public function detailPegawai()
    {
        return $this->hasMany(DetailPegawai::class, 'id_pegawai');
    }

    public function detailPegawaiStik()
    {
        return $this->hasMany(DetailPegawaiStik::class, 'id_pegawai');
    }
}

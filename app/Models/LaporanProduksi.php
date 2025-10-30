<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LaporanProduksi extends Model
{
    /**
     * Nama tabel yang terkait dengan model.
     *
     * @var string
     */

    /**
     * Primary key untuk model.
     *
     * @var string
     */

    protected $primaryKey = 'id';

    /**
     * Atribut yang dapat diisi massal.
     *
     * @var array
     */
    protected $fillable = [
        'id_mesin',
        'tanggal_produksi',
        'kendala',
    ];

    public function mesin()
    {
        // Asumsi Anda memiliki model 'Mesin'
        return $this->belongsTo(Mesin::class, 'id_mesin');
    }

    public function pegawaiRotary()
    {
        return $this->hasMany(PegawaiRotary::class, 'id_produksi');
    }

    public function penggunaanLahanRotary()
    {
        return $this->hasMany(PenggunaanLahanRotary::class, 'id_produksi');
    }

    public function detailHasilPaletRotary()
    {
        return $this->hasMany(DetailHasilPaletRotary::class, 'id_produksi');
    }

    public function validasiHasil()
    {
        return $this->hasMany(ValidasiHasilRotary::class, 'id_produksi');
    }
    public function gantiPisauRotary()
    {
        return $this->hasMany(GantiPisauRotary::class, 'id_produksi');
    }

    public function kayuPecahRotary()
    {
        return $this->hasMany(KayuPecahRotary::class, 'id_produksi');
    }
}

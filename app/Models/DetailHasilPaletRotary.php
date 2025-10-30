<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailHasilPaletRotary extends Model
{
    protected $table = 'detail_hasil_palet_rotaries';
    protected $primaryKey = 'id';
    //
    protected $fillable = [

        'id_produksi',
        'id_penggunaan_lahan',
        'produksi_rotaries',
        'timestamp_laporan',
        'id_ukuran',
        'kw',
        'palet',
        'total_lembar',
    ];
    //
    public function produksi()
    {
        return $this->belongsTo(ProduksiRotary::class, 'id_produksi');
    }

    public function setoranPaletUkuran()
    {
        return $this->belongsTo(Ukuran::class, 'id_ukuran');
    }
    public function penggunaanLahan()
    {
        return $this->belongsTo(PenggunaanLahanRotary::class, 'id_penggunaan_lahan', 'id');
    }

    public function lahan()
    {
        return $this->hasOneThrough(
            Lahan::class,
            PenggunaanLahanRotary::class,
            'id', // foreign key di tabel perantara
            'id',           // foreign key di tabel lahan
            'id',                 // primary key di produksi
            'id_lahan'            // local key di penggunaan_lahan_rotary
        );
    }
}

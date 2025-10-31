<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KendaraanSupplierKayu extends Model
{
    //
    protected $table = 'kendaraan_supplier_kayus';
    protected $fillable = [
        'nopol_kendaraan',
        'jenis_kendaraan',
        'pemilik_kendaraan',
        'kategori_kendaraan',
    ];
}

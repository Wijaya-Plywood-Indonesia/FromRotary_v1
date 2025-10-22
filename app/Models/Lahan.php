<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lahan extends Model
{
    protected $table = 'lahans';
    protected $primaryKey = 'id';
    //
    protected $fillable = [
        'kode_lahan',
        'nama_lahan',
        'detail',

    ];
    public function penggunaanLahanRotaries()
    {
        return $this->hasMany(PenggunaanLahanRotary::class, 'id_lahan');
    }
}

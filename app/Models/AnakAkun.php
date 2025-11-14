<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnakAkun extends Model
{
    //
    protected $table = 'anak_akuns';

    protected $fillable = [
        'id_induk_akun',
        'kode_anak_akun',
        'nama_anak_akun',
        'keterangan',
    ];

    /**
     * Relasi ke IndukAkun
     * Banyak Anak Akun milik satu Induk Akun
     */
    public function indukAkun()
    {
        return $this->belongsTo(IndukAkun::class, 'id_induk_akun');
    }

    /**
     * Relasi ke SubAnakAkun
     * Satu Anak Akun punya banyak Sub Anak Akun
     */
    public function subAnakAkuns()
    {
        return $this->hasMany(SubAnakAkun::class, 'id_anak_akun');
    }
}

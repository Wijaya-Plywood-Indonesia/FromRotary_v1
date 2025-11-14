<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IndukAkun extends Model
{
    //
    protected $table = 'induk_akuns';
    protected $fillable = [
        'kode_induk_akun',
        'nama_induk_akun',
        'keterangan',
    ];

    /**
     * Relasi ke AnakAkun
     * Satu Induk Akun punya banyak Anak Akun
     */
    public function anakAkuns()
    {
        return $this->hasMany(AnakAkun::class, 'id_induk_akun');
    }
    public function subAnakAkuns()
    {
        return $this->hasManyThrough(
            SubAnakAkun::class,
            AnakAkun::class,
            'id_induk_akun',     // FK di anak_akuns
            'id_anak_akun',      // FK di sub_anak_akuns
            'id',                // induk_akuns.id
            'id'                 // anak_akuns.id
        );
    }
}

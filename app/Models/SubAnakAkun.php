<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubAnakAkun extends Model
{
    //
    protected $table = 'sub_anak_akuns';

    protected $fillable = [
        'id_anak_akun',
        'kode_sub_anak_akun',
        'nama_sub_anak_akun',
        'keterangan',
    ];

    /**
     * Relasi ke AnakAkun
     * Banyak Sub Anak Akun milik satu Anak Akun
     */
    public function anakAkun()
    {
        return $this->belongsTo(AnakAkun::class, 'id_anak_akun');
    }
    public function indukAkun()
    {
        return $this->hasOneThrough(
            IndukAkun::class,
            AnakAkun::class,
            'id',            // FK anak → sub anak
            'id',            // FK induk → anak
            'id_anak_akun',  // local key sub anak
            'id_induk_akun'  // local key anak
        );
    }
}

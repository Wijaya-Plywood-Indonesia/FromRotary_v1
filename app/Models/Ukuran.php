<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ukuran extends Model
{
    //
    protected $fillable = [
        'panjang',
        'lebar',
        'tebal',

    ];
    public function targets()
    {
        return $this->hasMany(DetailHasilPaletRotary::class, 'id_ukuran', 'id');
    }
    public function getDimensiAttribute(): string
    {
        return "{$this->panjang} x {$this->lebar} x {$this->tebal}";
    }

    public function getNamaUkuranAttribute(): string
    {
        return "{$this->panjang}m x {$this->lebar}m x {$this->tebal}cm";
    }

    protected $appends = ['kubikasi', 'nama_ukuran'];
    public function getKubikasiAttribute()
    {
        return ($this->panjang ?? 0)
            * ($this->lebar ?? 0)
            * ($this->tebal ?? 0);
    }


}

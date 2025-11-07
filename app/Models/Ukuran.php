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

    protected $casts = [
        'panjang' => 'float',
        'lebar' => 'float',
        'tebal' => 'float',
    ];
    public function targets()
    {
        return $this->hasMany(DetailHasilPaletRotary::class, 'id_ukuran', 'id');
    }
    public function getDimensiAttribute(): string
    {
        return "{$this->panjang} x {$this->lebar} x {$this->tebal}";
    }
    protected $appends = ['kubikasi'];
    public function getKubikasiAttribute()
    {
        $panjang = (float) $this->panjang;
        $lebar = (float) $this->lebar;
        $tebal = (float) $this->tebal;

        return $panjang * $lebar * $tebal;
    }


}

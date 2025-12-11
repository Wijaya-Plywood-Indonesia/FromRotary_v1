<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProduksiSanding extends Model
{
    //

    protected $table = 'produksi_sandings';

    protected $fillable = [
        'tanggal',
        'kendala',
        'shift',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];
    public function modalSandings()
    {
        return $this->hasMany(ModalSanding::class, 'id_produksi_sanding');
    }
    public function hasilSandings()
    {
        return $this->hasMany(HasilSanding::class, 'id_produksi_sanding');
    }
}

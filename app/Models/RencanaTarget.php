<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RencanaTarget extends Model
{
    use HasFactory;

    protected $table = 'rencana_targets';

    protected $fillable = [
        'id_ukuran',
        'id_jenis_kayu',
        'kw',
    ];

    public function ukuran()
    {
        return $this->belongsTo(Ukuran::class, 'id_ukuran');
    }

    public function jenisKayu()
    {
        return $this->belongsTo(JenisKayu::class, 'id_jenis_kayu');
    }
}

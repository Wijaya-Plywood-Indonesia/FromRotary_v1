<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RencanaRepair extends Model
{
    use HasFactory;

    protected $table = 'rencana_repairs';

    protected $fillable = [
        'tanggal',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];
}

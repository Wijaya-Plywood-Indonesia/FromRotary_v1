<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ValidasiRepair extends Model
{
    protected $fillable = [
        'id_repair',
        'role',
        'status',
    ];

    public function repair()
    {
        return $this->belongsTo(Repair::class, 'id_repair');
    }
}

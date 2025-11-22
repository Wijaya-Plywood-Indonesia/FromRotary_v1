<?php

namespace App\Http\Controllers;

use App\Models\NotaBarangKeluar;
use App\Models\NotaKayu;

class NotaBKController extends Controller
{
    public function show(NotaBarangKeluar $record)
    {
        // Muat relasi yang diperlukan
        $record->load([
            'pembuat',
            'validator',
            'detail',
        ]);

        return view('nota-barang.bk-print', [
            'record' => $record,
            'details' => $record->detail,
        ]);
    }
}
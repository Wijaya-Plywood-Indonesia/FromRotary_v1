<?php

namespace App\Http\Controllers;

use App\Models\NotaBarangMasuk;

class NotaBMController extends Controller
{
    public function show(NotaBarangMasuk $record)
    {
        // Muat relasi yang diperlukan
        $record->load([
            'dibuatOleh',
            'divalidasiOleh',
            'detail',
        ]);

        return view('nota-barang.bm-print', [
            'record' => $record,
            'details' => $record->detail,
        ]);
    }
}
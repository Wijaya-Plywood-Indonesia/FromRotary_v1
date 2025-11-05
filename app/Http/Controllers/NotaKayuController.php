<?php

namespace App\Http\Controllers;

use App\Models\NotaKayu;

class NotaKayuController extends Controller
{
    public function show(NotaKayu $record)
    {
        // muat relasi kayuMasuk + detailMasukanKayu
        $record->load('kayuMasuk.detailMasukanKayu', 'kayuMasuk.penggunaanSupplier');

        return view('nota-kayu.print', compact('record'));
    }

}
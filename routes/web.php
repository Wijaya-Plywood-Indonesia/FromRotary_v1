<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NotaKayuController;
use App\Http\Controllers\NotaBKController;

Route::get('/nota-barang-keluar/{record}/print', [NotaBKController::class, 'show'])
    ->name('nota-bk.print');

Route::get('/nota-kayu/{record}', [NotaKayuController::class, 'show'])
    ->name('nota-kayu.show');

Route::get('/', function () {
    return view('welcome');
});


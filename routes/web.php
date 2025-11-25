<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NotaKayuController;
use App\Http\Controllers\NotaBKController;
use App\Http\Controllers\NotaBMController;

Route::get('/nota-barang-masuk/{record}/print', [NotaBMController::class, 'show'])
    ->name('nota-bm.print');

Route::get('/nota-barang-keluar/{record}/print', [NotaBKController::class, 'show'])
    ->name('nota-bk.print');

Route::get('/nota-kayu/{record}', [NotaKayuController::class, 'show'])
    ->name('nota-kayu.show');

Route::get('/', function () {
    return view('welcome');
});


<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NotaKayuController;
use App\Http\Controllers\NotaBKController;
use App\Http\Controllers\NotaBMController;
use App\Http\Controllers\LaporanKayuMasukController;

Route::get('/laporan-kayu-masuk', [LaporanKayuMasukController::class, 'index'])
    ->name('laporan.kayu-masuk');

Route::get('/laporan-kayu-masuk/export', [LaporanKayuMasukController::class, 'export'])
    ->name('laporan.kayu-masuk.export');

//Barang Masuk
Route::get('/nota-barang-masuk/{record}/print', [NotaBMController::class, 'show'])
    ->name('nota-bm.print');

Route::get('/nota-barang-masuk/rekap', [NotaBMController::class, 'rekap'])
    ->name('nota-bm.rekap');

Route::get('/nota-barang-keluar/{record}/print', [NotaBKController::class, 'show'])
    ->name('nota-bk.print');

Route::get('/nota-kayu/{record}', [NotaKayuController::class, 'show'])
    ->name('nota-kayu.show');

Route::get('/', function () {
    return view('welcome');
});


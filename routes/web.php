<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NotaKayuController;

Route::get('/nota-kayu/{record}', [NotaKayuController::class, 'show'])
    ->name('nota-kayu.show');

Route::get('/', function () {
    return view('welcome');
});


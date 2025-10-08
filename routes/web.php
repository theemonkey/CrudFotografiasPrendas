<?php

use App\Http\Controllers\ImageController;
use Illuminate\Support\Facades\Route;

// Página inicial para agregar fotos
Route::get('/', function () {
    return view('fotos-sit-add');
});

// Página principal de fotos ( index)
Route::get('/fotos', function () {
    return view('fotos-index');
})->name('fotos-index');

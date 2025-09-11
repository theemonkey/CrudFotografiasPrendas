<?php

use App\Http\Controllers\ImageController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('images.index');
});

Route::resource('images', ImageController::class);
Route::post('images/update-order', [ImageController::class, 'updateOrder'])->name('images.update-order');
Route::post('images/crop', [ImageController::class, 'cropImage'])->name('images.crop');

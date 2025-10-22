<?php

use App\Http\Controllers\FotografiaPrendaController;
use Illuminate\Support\Facades\Route;

// Página inicial para agregar fotos
/*Route::get('/', function () {
    return view('fotos-sit-add');
});

// Página principal de fotos ( index)
Route::get('/fotos', function () {
    return view('fotos-index');
})->name('fotos-index');*/

// Páginas principales
// 🎯 FLUJO CORRECTO: Primero agregar, luego ver índice
Route::get('/', [FotografiaPrendaController::class, 'create'])->name('home'); // Agregar fotos es la página principal
Route::get('/fotos', [FotografiaPrendaController::class, 'index'])->name('fotos.index'); // Ver índice después

// Rutas adicionales de compatibilidad
Route::get('/agregar', [FotografiaPrendaController::class, 'create'])->name('fotos.create');
Route::get('/fotos-index', [FotografiaPrendaController::class, 'index'])->name('fotos-index');

// API Routes para AJAX
Route::prefix('api/fotografias')->group(function () {
    // Obtener fotografías con filtros
    Route::get('/', [FotografiaPrendaController::class, 'obtenerFotografias'])->name('api.fotografias.index');

    // Subir fotografía individual
    Route::post('/', [FotografiaPrendaController::class, 'store'])->name('api.fotografias.store');

    // Subir múltiples fotografías (desde fotos-sit-add)
    Route::post('/multiple', [FotografiaPrendaController::class, 'storeMultiple'])->name('api.fotografias.store-multiple');

    // Actualizar fotografía
    Route::put('/{id}', [FotografiaPrendaController::class, 'update'])->name('api.fotografias.update');
    Route::patch('/{id}', [FotografiaPrendaController::class, 'update'])->name('api.fotografias.patch');

    // Eliminar fotografía
    Route::delete('/{id}', [FotografiaPrendaController::class, 'destroy'])->name('api.fotografias.destroy');

    // Datos para filtros predictivos
    Route::get('/filtros/datos', [FotografiaPrendaController::class, 'obtenerDatosFiltros'])->name('api.fotografias.filtros');
});

// Rutas adicionales de compatibilidad
Route::post('/fotos/upload', [FotografiaPrendaController::class, 'store'])->name('fotos.upload');
Route::post('/fotos/upload-multiple', [FotografiaPrendaController::class, 'storeMultiple'])->name('fotos.upload-multiple');

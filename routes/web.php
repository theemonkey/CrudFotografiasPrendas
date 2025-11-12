<?php

use App\Http\Controllers\FotografiaPrendaController;
use Illuminate\Support\Facades\Route;

// Páginas principales
// FLUJO CORRECTO: Primero agregar, luego ver índice
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
Route::post('/fotografias/limpiar-huerfanas', [FotografiaPrendaController::class, 'limpiarImagenesHuerfanas'])
    ->middleware('auth');


// Rutas simples para pruebas
/*Route::get('/fotos-sit-add', function () {
    return view('fotos-sit-add');
})->name('fotos-sit-add');

Route::get('/fotos-index', function () {
    return view('fotos-index');
})->name('fotos-index');

// Ruta por defecto
Route::get('/', function () {
    return redirect()->route('fotos-sit-add');
});

// APIs básicas para funcionamiento
Route::post('/api/fotografias', function () {
    return response()->json([
        'success' => true,
        'data' => [
            'id' => rand(1000, 9999),
            'imagen_url' => 'https://picsum.photos/400/600',
            'orden_sit' => request('orden_sit'),
            'po' => request('po'),
            'oc' => request('oc'),
            'descripcion' => request('descripcion'),
            'tipo' => request('tipo'),
            'created_at' => now()
        ]
    ]);
});

Route::get('/api/fotografias', function () {
    return response()->json(['success' => true, 'data' => []]);
});*/

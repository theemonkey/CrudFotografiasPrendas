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



/*Route::get('/', function () {
    return redirect()->route('images.index');
});

Route::resource('images', ImageController::class);
Route::post('images/update-order', [ImageController::class, 'updateOrder'])->name('images.update-order');
Route::post('images/crop', [ImageController::class, 'cropImage'])->name('images.crop');*/


// ========== NUEVAS RUTAS (Modal de Gestión Avanzada) cuando ya se prepare backend descomentar ==========

// Grupo de rutas para el modal de gestión de imágenes
/*Route::prefix('images-management')->name('images')->group(function () {

    // Obtener datos para el modal (con filtros, búsqueda, paginación)
    Route::get('/', [ImageController::class, 'fotos'])->name('index');

    // Búsqueda y filtrado
    Route::get('/search', [ImageManagementController::class, 'search'])->name('search');
    Route::post('/filter', [ImageManagementController::class, 'filter'])->name('filter');
    Route::delete('/clear-filters', [ImageManagementController::class, 'clearFilters'])->name('clear-filters');

    // Subida de imágenes (cámara y archivo)
    Route::post('/upload', [ImageManagementController::class, 'upload'])->name('upload');
    Route::post('/upload-camera', [ImageManagementController::class, 'uploadFromCamera'])->name('upload-camera');
    Route::post('/upload-file', [ImageManagementController::class, 'uploadFromFile'])->name('upload-file');

    // Operaciones CRUD específicas del modal
    Route::get('/{id}', [ImageManagementController::class, 'show'])->name('show');
    Route::put('/{id}', [ImageManagementController::class, 'update'])->name('update');
    Route::delete('/{id}', [ImageManagementController::class, 'destroy'])->name('destroy');

    // Comentarios
    Route::post('/{id}/comment', [ImageManagementController::class, 'addComment'])->name('add-comment');
    Route::get('/{id}/comments', [ImageManagementController::class, 'getComments'])->name('get-comments');
    Route::put('/comment/{commentId}', [ImageManagementController::class, 'updateComment'])->name('update-comment');
    Route::delete('/comment/{commentId}', [ImageManagementController::class, 'deleteComment'])->name('delete-comment');

    // Exportación
    Route::post('/export', [ImageManagementController::class, 'export'])->name('export');
    Route::post('/export-all', [ImageManagementController::class, 'exportAll'])->name('export-all');
    Route::post('/export-selected', [ImageManagementController::class, 'exportSelected'])->name('export-selected');

    // Operaciones en lote
    Route::post('/bulk-delete', [ImageManagementController::class, 'bulkDelete'])->name('bulk-delete');
    Route::post('/bulk-update', [ImageManagementController::class, 'bulkUpdate'])->name('bulk-update');

    // Ordenamiento y columnas
    Route::post('/sort', [ImageManagementController::class, 'sort'])->name('sort');
    Route::post('/toggle-columns', [ImageManagementController::class, 'toggleColumns'])->name('toggle-columns');

    // Estadísticas y reportes
    Route::get('/stats', [ImageManagementController::class, 'getStats'])->name('stats');
    Route::get('/reports', [ImageManagementController::class, 'getReports'])->name('reports');
});

// ========== RUTAS API PARA AJAX (opcional) ==========

// Grupo de rutas API para el modal (respuestas JSON)
Route::prefix('api/images-management')->name('api.images.management.')->group(function () {

    // Búsqueda en tiempo real
    Route::get('/search-suggestions', [ImageManagementController::class, 'getSearchSuggestions'])->name('search-suggestions');

    // Validaciones en tiempo real
    Route::post('/validate-sit', [ImageManagementController::class, 'validateSIT'])->name('validate-sit');
    Route::post('/validate-po', [ImageManagementController::class, 'validatePO'])->name('validate-po');
    Route::post('/validate-oc', [ImageManagementController::class, 'validateOC'])->name('validate-oc');

    // Obtener datos específicos
    Route::get('/get-image-data/{id}', [ImageManagementController::class, 'getImageData'])->name('get-image-data');
    Route::get('/get-filter-options', [ImageManagementController::class, 'getFilterOptions'])->name('get-filter-options');

    // Vista previa de imagen
    Route::get('/preview/{id}', [ImageManagementController::class, 'getImagePreview'])->name('preview');

    // Progreso de subida
    Route::get('/upload-progress/{uploadId}', [ImageManagementController::class, 'getUploadProgress'])->name('upload-progress');
});*/

// ========== RUTAS ADICIONALES (si las necesitas) ==========

// Ruta para mostrar el modal desde el menú lateral
/*Route::get('/management-modal', function () {
    return view('images.management-modal');
})->name('images.management.modal');

// Rutas para diferentes vistas/páginas
Route::get('/gallery', [ImageController::class, 'gallery'])->name('images.gallery');
Route::get('/dashboard', [ImageController::class, 'dashboard'])->name('images.dashboard');

// Rutas para configuración
Route::prefix('settings')->name('settings.')->group(function () {
    Route::get('/images', [ImageController::class, 'settings'])->name('images');
    Route::post('/images', [ImageController::class, 'updateSettings'])->name('images.update');
});*/

// ========== RUTAS DE AUTENTICACIÓN (si las necesitas) ==========
/*
Route::middleware(['auth'])->group(function () {
    // Todas las rutas que requieren autenticación
    Route::resource('images', ImageController::class);
    // ... otras rutas
});
*/

// ========== RUTAS DE ADMINISTRACIÓN (si las necesitas) ==========
/*
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/images/analytics', [ImageController::class, 'analytics'])->name('images.analytics');
    Route::post('/images/bulk-operations', [ImageController::class, 'bulkOperations'])->name('images.bulk');
});
*/

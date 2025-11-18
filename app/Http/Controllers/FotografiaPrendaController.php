<?php
// filepath: app/Http/Controllers/FotografiaPrendaController.php

namespace App\Http\Controllers;

use App\Models\FotografiaPrenda;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Carbon\Carbon;

class FotografiaPrendaController extends Controller
{
    // ==== Mostrar página principal( Agregar Foto) ====
    public function create()
    {
        return view('fotos-sit-add');
    }

    // ==== Mostrar página de index Después de agregar ====
    public function index()
    {
        return view('fotos-index');
    }

    // ==== Obtener fotografías con filtros y paginación ====

    public function obtenerFotografias(Request $request)
    {
        try {
            $query = FotografiaPrenda::query();

            // Aplicar filtros
            if ($request->filled('orden_sit')) {
                $query->porOrdenSit($request->orden_sit);
            }

            if ($request->filled('po')) {
                $query->porPO($request->po);
            }

            if ($request->filled('oc')) {
                $query->porOC($request->oc);
            }

            if ($request->filled('descripcion')) {
                $query->porDescripcion($request->descripcion);
            }

            if ($request->filled('tipo')) {
                $query->porTipo($request->tipo);
            }

            // Filtro por rango de fechas
            if ($request->filled('fecha_inicio') && $request->filled('fecha_fin')) {
                $query->entreFechas($request->fecha_inicio, $request->fecha_fin);
            }

            // Búsqueda global
            if ($request->filled('buscar')) {
                $busqueda = $request->buscar;
                $query->where(function ($q) use ($busqueda) {
                    $q->where('orden_sit', 'like', "%{$busqueda}%")
                        ->orWhere('po', 'like', "%{$busqueda}%")
                        ->orWhere('oc', 'like', "%{$busqueda}%")
                        ->orWhere('descripcion', 'like', "%{$busqueda}%");
                });
            }

            // Ordenamiento
            $query->orderBy('created_at', 'desc');

            // Paginación
            $perPage = $request->get('per_page', 10);
            $fotografias = $query->paginate($perPage);

            // Formatear datos para el frontend con URLS correctas
            $data = $fotografias->through(function ($foto) {
                //CONSTRUIR URL correcta según si ya tiene URL completa o no
                $imageUrl = $foto->imagen_url;

                if (!$imageUrl || !filter_var($imageUrl, FILTER_VALIDATE_URL)) {
                    // Si no tiene URL válida, construir desde imagen_path
                    $imageUrl = asset('storage/' . $foto->imagen_path);
                }

                //VERIFICAR que el archivo existe
                $imagePath = public_path('storage/' . $foto->imagen_path);
                $fileExists = file_exists($imagePath);

                if (!$fileExists) {
                    \Log::warning('Archivo de imagen no encontrado', [
                        'id' => $foto->id,
                        'path' => $foto->imagen_path,
                        'full_path' => $imagePath,
                        'url' => $imageUrl
                    ]);

                    //USAR imagen por defecto si no existe
                    $imageUrl = asset('images/default-image.jpg'); // o cualquier imagen por defecto
                }

                return [
                    'id' => $foto->id,
                    'orden_sit' => $foto->orden_sit,
                    'po' => $foto->po,
                    'oc' => $foto->oc ?? '-',
                    'descripcion' => $foto->descripcion,
                    'tipo' => $foto->tipo,
                    'imagen_url' => $imageUrl,
                    'imagen_path' => $foto->imagen_path,
                    'imagen_size_formatted' => $foto->imagen_size_formatted ?? 'N/A',
                    'fecha_subida' => $foto->created_at->format('d-m-Y H:i'),
                    'created_at' => $foto->created_at->toISOString(),
                    'subido_por' => $foto->subido_por ?? 'Sistema',
                    'file_exists' => $fileExists
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $data->items(),
                'pagination' => [
                    'current_page' => $fotografias->currentPage(),
                    'last_page' => $fotografias->lastPage(),
                    'per_page' => $fotografias->perPage(),
                    'total' => $fotografias->total(),
                    'from' => $fotografias->firstItem(),
                    'to' => $fotografias->lastItem()
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Error obteniendo fotografías: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener fotografías'
            ], 500);
        }
    }

    // ==== Subir nueva fotografía ====
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'imagen' => 'required|image|mimes:jpeg,png,jpg,gif|max:10240', // 10MB máximo
            'orden_sit' => 'required|string|max:20',
            'po' => 'required|string|max:20',
            'oc' => 'nullable|string|max:20',
            'descripcion' => 'required|string|max:500',
            'tipo' => 'required|in:MUESTRA,PRENDA FINAL,VALIDACION AC'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $imagen = $request->file('imagen');

            // Generar HASH único para la imagen
            $fileContent = file_get_contents($imagen->getPathname());
            $hashContent = hash('sha256', $fileContent);
            $base64Hash = base64_encode($hashContent);

            //Limpiar Base64 para nombre de archivo
            $cleanHash = str_replace(['+', '/', '='], ['-', '_', ''], $base64Hash);
            $cleanHash = substr($cleanHash, 0, 32); // Limitar Longitud
            $nombreArchivo = $cleanHash . '.jpeg';

            // Organizar por año/mes
            $rutaDestino = 'fotografias/' . date('Y') . '/' . date('m');

            // Guardar imagen
            $rutaCompleta = $rutaDestino . '/' . $nombreArchivo;

            // Verificar si ya existe archivo con mismo HASH
            $existingPhoto = FotografiaPrenda::where('imagen_path', 'LIKE', '%' . $cleanHash . '%')->first();

            if ($existingPhoto && Storage::disk('public')->exists($rutaCompleta)) {
                //Archivo duplicado - Reutilizar imagen existente
                $imageUrl = asset('storage/' . $existingPhoto->imagen_path);
                $rutaFinal = $existingPhoto->imagen_path;
            } else {
                // Archivo nuevo - Guardarlo
                $rutaFinal = $imagen->storeAs($rutaDestino, $nombreArchivo, 'public');
                $imageUrl = asset('storage/' . $rutaFinal);
            }

            // Crear registro en BD
            $fotografia = FotografiaPrenda::create([
                'orden_sit' => $request->orden_sit,
                'po' => $request->po,
                'oc' => $request->oc,
                'descripcion' => $request->descripcion,
                'tipo' => $request->tipo,
                'imagen_path' => $rutaFinal,
                'imagen_url' => $imageUrl,
                'imagen_original_name' => $imagen->getClientOriginalName(),
                'imagen_size' => $imagen->getSize(),
                'imagen_mime_type' => $imagen->getMimeType(),
                'fecha_subida' => now(),
                'subido_por' => auth()->user()->name ?? 'Sistema',
                'metadatos' => [
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'upload_timestamp' => time(),
                    'origen_vista' => $request->origen_vista ?? 'unknown',
                    'hash_sha256' => $hashContent, // Guardar hash original
                    'hash_filename' => $cleanHash  // Guardar hash limpio
                ]
            ]);

            // Verificar que el archivo realmente existe
            $fullPath = public_path('storage/' . $rutaFinal);
            if (!file_exists($fullPath)) {
                \Log::error('Archivo no encontrado: ' . $fullPath);
                throw new \Exception('Archivo no se guardó correctamente');
            }

            \Log::info('Fotografía subida correctamente', [
                'id' => $fotografia->id,
                'hash' => $cleanHash,
                'path' => $rutaFinal,
                'url' => $imageUrl,
                'file_exists' => file_exists($fullPath),
                'is_duplicate' => isset($existingPhoto)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Fotografía subida correctamente',
                'data' => [
                    'id' => $fotografia->id,
                    'orden_sit' => $fotografia->orden_sit,
                    'po' => $fotografia->po,
                    'oc' => $fotografia->oc ?? '-',
                    'descripcion' => $fotografia->descripcion,
                    'tipo' => $fotografia->tipo,
                    'imagen_url' => $imageUrl,
                    'imagen_path' => $rutaFinal,
                    'fecha_subida' => $fotografia->created_at->format('d-m-Y H:i'),
                    'hash_filename' => $cleanHash,
                    'created_at' => $fotografia->created_at,
                    'is_duplicate' => isset($existingPhoto)
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Error subiendo fotografía: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al subir la fotografía'
            ], 500);
        }
    }

    // ==== Actualizar fotografía existente ====
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'orden_sit' => 'sometimes|required|string|max:20',
            'po' => 'sometimes|required|string|max:20',
            'oc' => 'nullable|string|max:20',
            'descripcion' => 'sometimes|required|string|max:500',
            'tipo' => 'sometimes|required|in:MUESTRA,PRENDA FINAL,VALIDACION AC',
            'imagen' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:10240'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $fotografia = FotografiaPrenda::findOrFail($id);

            //GUARDAR RUTA ANTERIOR PARA ELIMINACIÓN (DESPUÉS DEL PROCESAMIENTO)
            $rutaAnterior = $fotografia->imagen_path;
            $hashAnterior = isset($fotografia->metadatos['hash_filename']) ? $fotografia->metadatos['hash_filename'] : null;

            // Actualizar campos de texto
            $fotografia->fill($request->only([
                'orden_sit',
                'po',
                'oc',
                'descripcion',
                'tipo'
            ]));

            // Si hay nueva imagen, procesarla
            if ($request->hasFile('imagen')) {
                \Log::info('Actualizando imagen', [
                    'fotografia_id' => $id,
                    'ruta_anterior' => $rutaAnterior,
                    'hash_anterior' => $hashAnterior
                ]);

                $imagen = $request->file('imagen');

                //GENERAR NUEVO HASH ÚNICO (SIN ELIMINAR LA ANTERIOR AÚN)
                $fileContent = file_get_contents($imagen->getPathname());
                $hashContent = hash('sha256', $fileContent);
                $base64Hash = base64_encode($hashContent);
                $cleanHash = str_replace(['+', '/', '='], ['-', '_', ''], $base64Hash);
                $cleanHash = substr($cleanHash, 0, 32);
                $nombreArchivo = $cleanHash . '.jpeg';

                $rutaDestino = 'fotografias/' . date('Y') . '/' . date('m');
                $rutaNueva = $rutaDestino . '/' . $nombreArchivo;

                \Log::info('Hash generado para nueva imagen', [
                    'hash_anterior' => $hashAnterior,
                    'hash_nuevo' => $cleanHash,
                    'nombre_archivo' => $nombreArchivo,
                    'ruta_nueva' => $rutaNueva
                ]);

                //VERIFICAR SI YA EXISTE IMAGEN CON MISMO HASH (pero diferente ID)
                $existingPhoto = FotografiaPrenda::where('imagen_path', 'LIKE', '%' . $cleanHash . '%')
                    ->where('id', '!=', $id)
                    ->first();

                if ($existingPhoto && Storage::disk('public')->exists($rutaNueva)) {
                    //REUTILIZAR IMAGEN EXISTENTE
                    $fotografia->imagen_path = $existingPhoto->imagen_path;
                    $isReutilizada = true;

                    \Log::info('Reutilizando imagen existente en actualización', [
                        'nuevo_hash' => $cleanHash,
                        'existing_id' => $existingPhoto->id,
                        'ruta_reutilizada' => $existingPhoto->imagen_path
                    ]);
                } else {
                    //GUARDAR NUEVA IMAGEN CON HASH
                    $rutaCompleta = $imagen->storeAs($rutaDestino, $nombreArchivo, 'public');
                    $fotografia->imagen_path = $rutaCompleta;
                    $isReutilizada = false;

                    \Log::info('Nueva imagen guardada en actualización', [
                        'nuevo_hash' => $cleanHash,
                        'nueva_ruta' => $rutaCompleta,
                        'archivo_guardado' => Storage::disk('public')->exists($rutaCompleta)
                    ]);
                }

                //ACTUALIZAR METADATOS CON NUEVO HASH
                $fotografia->imagen_original_name = $imagen->getClientOriginalName();
                $fotografia->imagen_size = $imagen->getSize();
                $fotografia->imagen_mime_type = $imagen->getMimeType();

                $metadatos = $fotografia->metadatos ?? [];
                $metadatos['hash_sha256_anterior'] = $metadatos['hash_sha256'] ?? 'no_disponible';
                $metadatos['hash_filename_anterior'] = $metadatos['hash_filename'] ?? 'no_disponible';
                $metadatos['hash_sha256'] = $hashContent;
                $metadatos['hash_filename'] = $cleanHash;
                $metadatos['updated_at'] = now()->toISOString();
                $metadatos['is_reutilizada'] = $isReutilizada;
                $metadatos['update_reason'] = 'imagen_editada';

                $fotografia->metadatos = $metadatos;

                //GUARDAR CAMBIOS EN BD PRIMERO
                $fotografia->save();

                //AHORA SÍ ELIMINAR IMAGEN ANTERIOR (DESPUÉS DE GUARDAR LA NUEVA)
                if ($rutaAnterior && $rutaAnterior !== $fotografia->imagen_path) {
                    // Verificar si alguna otra fotografía usa la imagen anterior
                    $otrosUsos = FotografiaPrenda::where('imagen_path', $rutaAnterior)
                        ->where('id', '!=', $id)
                        ->count();

                    if ($otrosUsos === 0) {
                        //ELIMINAR FÍSICAMENTE SI NO LA USA NADIE MÁS
                        if (Storage::disk('public')->exists($rutaAnterior)) {
                            Storage::disk('public')->delete($rutaAnterior);
                            \Log::info('Imagen anterior eliminada', [
                                'ruta_eliminada' => $rutaAnterior,
                                'razon' => 'no_otros_usos'
                            ]);
                        }
                    } else {
                        \Log::info('Imagen anterior NO eliminada', [
                            'ruta_conservada' => $rutaAnterior,
                            'otros_usos' => $otrosUsos,
                            'razon' => 'usada_por_otros_registros'
                        ]);
                    }
                }

                //VERIFICAR QUE EL ARCHIVO NUEVO EXISTA
                $fullPath = public_path('storage/' . $fotografia->imagen_path);
                if (!file_exists($fullPath)) {
                    \Log::error('Archivo actualizado no encontrado: ' . $fullPath);
                    throw new \Exception('La imagen actualizada no se guardó correctamente');
            }

                \Log::info('Fotografía actualizada correctamente', [
                    'id' => $fotografia->id,
                    'nueva_imagen' => true,
                    'hash_anterior' => $hashAnterior,
                    'hash_nuevo' => $cleanHash,
                    'ruta_anterior' => $rutaAnterior,
                    'ruta_nueva' => $fotografia->imagen_path,
                    'archivo_existe' => file_exists($fullPath)
                ]);
            } else {
                //SOLO ACTUALIZACIÓN DE CAMPOS SIN IMAGEN
                $fotografia->save();

                \Log::info('Fotografía actualizada sin cambio de imagen', [
                    'id' => $fotografia->id,
                    'campos_actualizados' => $request->only(['orden_sit', 'po', 'oc', 'descripcion', 'tipo'])
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Fotografía actualizada correctamente',
                'data' => [
                    'id' => $fotografia->id,
                    'orden_sit' => $fotografia->orden_sit,
                    'po' => $fotografia->po,
                    'oc' => $fotografia->oc ?? '-',
                    'descripcion' => $fotografia->descripcion,
                    'tipo' => $fotografia->tipo,
                    'imagen_url' => $fotografia->imagen_url,
                    'imagen_path' => $fotografia->imagen_path,
                    'fecha_subida' => $fotografia->created_at->format('d-m-Y H:i'),
                    'hash_filename' => isset($cleanHash) ? $cleanHash : ($fotografia->metadatos['hash_filename'] ?? 'sin_cambios'),
                    'created_at' => $fotografia->created_at,
                    'updated_at' => $fotografia->updated_at,
                    'imagen_actualizada' => isset($rutaNueva)
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Error actualizando fotografía: ' . $e->getMessage(), [
                'id' => $id,
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar la fotografía: ' . $e->getMessage()
            ], 500);
        }
    }

    // ==== Eliminar fotografía ====
    public function destroy($id)
    {
        try {
            $fotografia = FotografiaPrenda::findOrFail($id);

            // La imagen física se elimina automáticamente por el event listener
            $fotografia->delete();

            return response()->json([
                'success' => true,
                'message' => 'Fotografía eliminada correctamente'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error eliminando fotografía: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la fotografía'
            ], 500);
        }
    }

    // ==== Obtener datos para filtros predictivos ====
    public function obtenerDatosFiltros()
    {
        try {
            $datos = [
                'ordenes_sit' => FotografiaPrenda::distinct()->pluck('orden_sit')->filter()->take(50),
                'pos' => FotografiaPrenda::distinct()->pluck('po')->filter()->take(50),
                'ocs' => FotografiaPrenda::distinct()->pluck('oc')->filter()->take(50),
                'descripciones' => FotografiaPrenda::distinct()->pluck('descripcion')->filter()->take(50),
                'tipos' => ['MUESTRA', 'PRENDA FINAL', 'VALIDACION AC'],
                'estadisticas' => [
                    'total' => FotografiaPrenda::count(),
                    'por_tipo' => FotografiaPrenda::selectRaw('tipo, COUNT(*) as count')
                        ->groupBy('tipo')
                        ->pluck('count', 'tipo'),
                    'este_mes' => FotografiaPrenda::whereMonth('created_at', now()->month)
                        ->whereYear('created_at', now()->year)
                        ->count()
                ]
            ];

            return response()->json([
                'success' => true,
                'data' => $datos
            ]);
        } catch (\Exception $e) {
            \Log::error('Error obteniendo datos de filtros: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener datos de filtros'
            ], 500);
        }
    }

    // ==== Subir múltiples fotografías (para el flujo de fotos-sit-add) ====
    public function storeMultiple(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fotografias' => 'required|array|min:1',
            'fotografias.*.imagen' => 'required|string', // Base64
            'fotografias.*.orden_sit' => 'required|string|max:20',
            'fotografias.*.po' => 'required|string|max:20',
            'fotografias.*.oc' => 'nullable|string|max:20',
            'fotografias.*.descripcion' => 'required|string|max:500',
            'fotografias.*.tipo' => 'required|in:MUESTRA,PRENDA FINAL,VALIDACION AC'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $fotografiasCreadas = [];
            $errores = [];
            $reutilizadas = 0;

            foreach ($request->fotografias as $index => $fotoData) {
                try {
                    // Decodificar imagen base64
                    $imagenBase64 = $fotoData['imagen'];
                    if (preg_match('/^data:image\/(\w+);base64,/', $imagenBase64, $matches)) {
                        $extension = $matches[1];
                        $imagenBase64 = substr($imagenBase64, strpos($imagenBase64, ',') + 1);
                        $imagenBinaria = base64_decode($imagenBase64);

                        // Generar HASH único del contenido de la imagen
                        $hashContent = hash('sha256', $imagenBinaria);
                        $base64Hash = base64_encode($hashContent);
                        $cleanHash = str_replace(['+', '/', '='], ['', '', ''], $base64Hash);
                        $cleanHash = substr($cleanHash, 0, 32);
                        $nombreArchivo = $cleanHash . '.jpeg';

                        $rutaDestino = 'fotografias/' . date('Y') . '/' . date('m');
                        $rutaCompleta = $rutaDestino . '/' . $nombreArchivo;

                        // Verificar si ya existe
                        $existingPhoto = FotografiaPrenda::where('imagen_path', 'LIKE', '%' . $cleanHash . '%')->first();

                        if ($existingPhoto && Storage::disk('public')->exists($rutaCompleta)) {
                            // Reutilizar imagen existente
                            $rutaFinal = $existingPhoto->imagen_path;
                            $reutilizadas++;
                        } else {
                            // Guardar nueva imagen
                            if (!Storage::disk('public')->exists($rutaDestino)) {
                                Storage::disk('public')->makeDirectory($rutaDestino);
                            }

                            Storage::disk('public')->put($rutaCompleta, $imagenBinaria);
                            $rutaFinal = $rutaCompleta;
                        }

                        // Crear registro
                        $fotografia = FotografiaPrenda::create([
                            'orden_sit' => $fotoData['orden_sit'],
                            'po' => $fotoData['po'],
                            'oc' => $fotoData['oc'] ?? null,
                            'descripcion' => $fotoData['descripcion'],
                            'tipo' => $fotoData['tipo'],
                            'imagen_path' => $rutaFinal,
                            'imagen_original_name' => $fotoData['nombre'] ?? $nombreArchivo,
                            'imagen_size' => strlen($imagenBinaria),
                            'imagen_mime_type' => 'image/' . $extension,
                            'fecha_subida' => now(),
                            'subido_por' => auth()->user()->name ?? 'Sistema',
                            'metadatos' => [
                                'hash_sha256' => $hashContent,
                                'hash_filename' => $cleanHash,
                                'batch_index' => $index,
                                'is_duplicate' => isset($existingPhoto)
                            ]
                        ]);

                        $fotografiasCreadas[] = [
                            'id' => $fotografia->id,
                            'orden_sit' => $fotografia->orden_sit,
                            'po' => $fotografia->po,
                            'oc' => $fotografia->oc ?? '-',
                            'descripcion' => $fotografia->descripcion,
                            'tipo' => $fotografia->tipo,
                            'imagen_url' => $fotografia->imagen_url,
                            'hash_filename' => $cleanHash,
                            'is_duplicate' => isset($existingPhoto)
                        ];
                    } else {
                        $errores[] = "Imagen {$index}: Formato base64 inválido";
                    }
                } catch (\Exception $e) {
                    $errores[] = "Imagen {$index}: " . $e->getMessage();
                }
            }

            return response()->json([
                'success' => true,
                'message' => count($fotografiasCreadas) . ' fotografía(s) subida(s) correctamente',
                'data' => [
                    'fotografias_creadas' => $fotografiasCreadas,
                    'total_creadas' => count($fotografiasCreadas),
                    'errores' => $errores
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Error subiendo múltiples fotografías: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al subir las fotografías'
            ], 500);
        }
    }

    /*=========================================================================================================== */
    // Limpiar imágenes huérfanas o las que no estan siendo usadas por ningún registro
    public function limpiarImagenesHuerfanas()
    {
        try {
            $imagenesEnBD = FotografiaPrenda::pluck('imagen_path')->filter()->unique();
            $imagenesEnStorage = Storage::disk('public')->allFiles('fotografias');

            $imagenesHuerfanas = array_diff($imagenesEnStorage, $imagenesEnBD->toArray());
            $eliminadas = 0;

            foreach ($imagenesHuerfanas as $imagen) {
                if (Storage::disk('public')->exists($imagen)) {
                    Storage::disk('public')->delete($imagen);
                    $eliminadas++;
                    \Log::info('Imagen huérfana eliminada: ' . $imagen);
                }
            }

            return response()->json([
                'success' => true,
                'message' => "{$eliminadas} imagen(es) huérfana(s) eliminada(s)",
                'data' => [
                    'eliminadas' => $eliminadas,
                    'imagenes_huerfanas' => $imagenesHuerfanas
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Error limpiando imágenes huérfanas: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al limpiar imágenes huérfanas'
            ], 500);
        }
    }
}

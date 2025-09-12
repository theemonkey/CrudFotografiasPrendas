<?php

namespace App\Http\Controllers;

use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;


class ImageController extends Controller
{
    protected $stages = [
        'diseño',
        'confeccion',
        'acabado',
        'control_calidad',
        'empaque'
    ];

    public function index(Request $request)
    {
        $query = Image::query()->ordered();

        if ($request->filled('stage')) {
            $query->byStage($request->stage);
        }
        //Obtener conjunto de imagenes del resultado de consulta(consulta a DB de manera paginada)
        $images = $query->paginate(12);
        $stages = $this->stages;

        return view('index', compact('images', 'stages'));
    }
    // ===>>>CRUD<<<===
    public function create()
    {
        $stages = $this->stages;
        return view('create', compact('stages'));
    }

    public function store(Request $request)
    {
        try {
            // Debug temporal
            \Log::info('Datos recibidos:', $request->all());
            //Pasar array como segundo parametro
            $fileCount = $request->hasFile('images') ? count($request->file('images')) : 0;
            \Log::info('Archivos recibidos:', ['count' => $fileCount]);

            $request->validate([
                'images' => 'required|array',
                'images.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:10240',
                'descripciones' => 'required|array',
                'descripciones.*' => 'required|string|max:255',
                'etapa_prenda' => 'required|string|in:' . implode(',', $this->stages),
                'orden_posiciones' => 'required|array|min:1',
                'orden_posiciones.*' => 'required|integer|min:1',
            ]);


            \Log::info('Validación pasada');

            $uploadedImages = [];

            foreach ($request->file('images') as $index => $file) {
                \Log::info("Procesando archivo {$index}: " . $file->getClientOriginalName());

                $description = $request->descripciones[$index] ?? "Imagen {$index}";
                $orderPosition = ($index + 1);

                //Generar nombres unicos
                //$timestamp = now()->format('YmdHis');
                $random = substr(md5(uniqid()), 0, 8);
                $extension = $file->getClientOriginalExtension();

                //Rutas de almacenamiento
                $filename = "img_{$random}_{$index}";
                $thumbnailPath = "thumbnails/{$filename}_thumb.{$extension}";
                $fullSizePath = "images/{$filename}.{$extension}";

                \Log::info("Guardando archivo en: {$fullSizePath}");

                // ==>>> Guardar archivo original directamente (sin redimensionar)
                $path = $file->storeAs('images', "{$filename}.{$extension}", 'public');
                \Log::info("Archivo guardado en: {$path}");

                // Reemplaza las líneas comentadas con esto:
                $manager = new \Intervention\Image\ImageManager(
                    new \Intervention\Image\Drivers\Gd\Driver()
                );

                $fullImage = $manager->read($file);

                // Redimensionar si es muy grande
                if ($fullImage->width() > 1920 || $fullImage->height() > 1080) {
                    $fullImage->scale(width: 1920, height: 1080);
                }

                // Guardar imagen completa
                $encodedFull = $fullImage->encode(new \Intervention\Image\Encoders\AutoEncoder(quality: 85));
                Storage::disk('public')->put($fullSizePath, $encodedFull);

                // Crear thumbnail (300x300)
                $thumbnail = $fullImage->cover(300, 300);
                $encodedThumb = $thumbnail->encode(new \Intervention\Image\Encoders\AutoEncoder(quality: 80));
                Storage::disk('public')->put($thumbnailPath, $encodedThumb);

                // Guardar en base de datos
                $image = Image::create([
                    'descripcion' => $description,
                    'orden_posicion' => $orderPosition,
                    'etapa_prenda' => $request->etapa_prenda,
                    'tamanio_miniatura' => $thumbnailPath,
                    'tamanio_completo' => $fullSizePath,
                    'nombre_original' => $file->getClientOriginalName(),
                    'tamanio_del_archivo' => $file->getSize(),
                ]);
                \Log::info("Imagen guardada en BD con ID: {$image->id}");
                $uploadedImages[] = $image;
            }
            \Log::info('=== STORE COMPLETADO EXITOSAMENTE ===');

            return response()->json([
                'success' => true,
                'message' => 'imagenes guardadas exitosamente',
                'images' => $uploadedImages,
                'count' => count($uploadedImages)
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Error de validación:', $e->errors());
            return response()->json([
                'success' => false,
                'message' => 'Error de validacion',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error en store: ', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al guardar las imagenes: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(Image $image)
    {
        return view('images.show', compact('image'));
    }

    public function edit(Image $image)
    {
        $stages = $this->stages;
        return view('edit', compact('image', 'stages'));
    }

    public function update(Request $request, Image $image)
    {
        try {

            $request->validate([
                'descripcion' => 'required|string|max:255',
                'orden_posicion' => 'required|integer|min:1',
                'etapa_prenda' => 'required|string|in:' . implode(',', $this->stages),
            ]);

            $image->update([
                'descripcion' => $request->descripcion,
                'orden_posicion' => $request->orden_posicion,
                'etapa_prenda' => $request->etapa_prenda,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Imagen actualizada exitosamente',
                'image' => $image->fresh()
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validacion',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error in update: ', [
                'message' => $e->getMessage(),
                'line' => $e->getLine()
            ]);

            return response()->json([
                'sucess' => false,
                'message' => 'Error al actualizar: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Image $image)
    {
        $image->deleteFiles();
        $image->delete();

        return response()->json([
            'success' => true,
            'message' => 'Imagen eliminada exitosamente'
        ]);
    }

    public function updateOrder(Request $request)
    {
        $request->validate([
            'images' => 'required|array',
            'images.*.id' => 'required|exists:images,id',
            'images.*.orden_posicion' => 'required|integer|min:1',
        ]);

        foreach ($request->images as $imageData) {
            Image::where('id', $imageData['id'])
                ->update(['orden_posicion' => $imageData['orden_posicion']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Orden actualizado exitosamente'
        ]);
    }

    public function cropImage(Request $request)
    {
        try {
            \Log::info('=== CROP IMAGE REQUEST ===', $request->all());

            $request->validate([
                'image_id' => 'required|exists:images,id',
                'x' => 'required|numeric',
                'y' => 'required|numeric',
                'width' => 'required|numeric|min:1',
                'height' => 'required|numeric|min:1',
                'rotate' => 'nullable|numeric',
                'scaleX' => 'nullable|numeric',
                'scaleY' => 'nullable|numeric',
            ]);

            $image = Image::findOrFail($request->image_id);
            \Log::info('Image found:', ['id' => $image->id, 'path' => $image->tamanio_completo]);

            // Cargar la imagen original
            $originalPath = storage_path('app/public/' . $image->tamanio_completo);
            \Log::info('Original path:', ['path' => $originalPath]);

            if (!file_exists($originalPath)) {
                \Log::error('File not found:', ['path' => $originalPath]);
                return response()->json([
                    'success' => false,
                    'message' => 'Archivo original no encontrado: ' . $originalPath
                ], 404);
            }

            // SINTAXIS PARA INTERVENTION IMAGE V3
            $manager = new \Intervention\Image\ImageManager(
                new \Intervention\Image\Drivers\Gd\Driver()
            );

            $img = $manager->read($originalPath);
            \Log::info('Image loaded successfully with v3');

            // Aplicar transformaciones si existen
            if (!empty($request->rotate) && $request->rotate != 0) {
                $img->rotate(-$request->rotate);
                \Log::info('Rotation applied:', ['degrees' => -$request->rotate]);
            }

            // Aplicar escala si existe
            if (!empty($request->scaleX) && $request->scaleX != 1) {
                $img->flip('horizontal');
                \Log::info('Horizontal flip applied');
            }
            if (!empty($request->scaleY) && $request->scaleY != 1) {
                $img->flip('vertical');
                \Log::info('Vertical flip applied');
            }

            // Aplicar recorte (sintaxis v3)
            $img->crop(
                (int) $request->width,
                (int) $request->height,
                (int) $request->x,
                (int) $request->y
            );
            \Log::info('Crop applied:', [
                'x' => (int) $request->x,
                'y' => (int) $request->y,
                'width' => (int) $request->width,
                'height' => (int) $request->height
            ]);

            // Generar nuevos nombres de archivo
            //$timestamp = now()->format('YmdHis');
            $random = substr(md5(uniqid()), 0, 8);
            $extension = pathinfo($image->tamanio_completo, PATHINFO_EXTENSION);

            $filename = "img_{$random}";
            $newThumbnailPath = "thumbnails/{$filename}_thumb.{$extension}";
            $newFullSizePath = "images/{$filename}.{$extension}";

            \Log::info('New paths generated:', [
                'full' => $newFullSizePath,
                'thumb' => $newThumbnailPath
            ]);

            // CREAR directorios
            Storage::disk('public')->makeDirectory('images');
            Storage::disk('public')->makeDirectory('thumbnails');

            // Guardar imagen recortada (sintaxis v3)
            $encodedImage = $img->encode(new \Intervention\Image\Encoders\AutoEncoder(quality: 85));
            Storage::disk('public')->put($newFullSizePath, $encodedImage);
            \Log::info('Full size image saved');

            // Crear nuevo thumbnail (sintaxis v3)
            $thumbnail = $img->cover(300, 300); // cover es el equivalente a fit() en v3
            $encodedThumbnail = $thumbnail->encode(new \Intervention\Image\Encoders\AutoEncoder(quality: 80));
            Storage::disk('public')->put($newThumbnailPath, $encodedThumbnail);
            \Log::info('Thumbnail created');

            // Eliminar archivos antiguos
            $image->deleteFiles();
            \Log::info('Old files deleted');

            // Actualizar rutas en la base de datos
            $image->update([
                'tamanio_miniatura' => $newThumbnailPath,
                'tamanio_completo' => $newFullSizePath,
                'tamanio_del_archivo' => Storage::disk('public')->size($newFullSizePath),
            ]);
            \Log::info('Database updated successfully');

            \Log::info('=== CROP COMPLETED SUCCESSFULLY ===');

            return response()->json([
                'success' => true,
                'message' => 'Imagen recortada correctamente',
                'image' => $image->fresh(),
                'thumbnail_url' => asset('storage/' . $newThumbnailPath),
                'full_size_url' => asset('storage/' . $newFullSizePath)
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation error in crop:', ['errors' => $e->errors()]);
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error in cropImage:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al procesar la imagen: ' . $e->getMessage()
            ], 500);
        }
    }
}

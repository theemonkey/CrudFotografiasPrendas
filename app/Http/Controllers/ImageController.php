<?php

namespace App\Http\Controllers;

use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image as InterventionImage;
use Illuminate\Support\Str;

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

        // Debug temporal
        \Log::info('Request data:', $request->all());
        \Log::info('Files:', $request->file());

        // ... resto del código

        $request->validate([
            'images' => 'required|array',
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:10240',
            'descripciones' => 'required|array',
            'descripciones.*' => 'required|string|max:255',
            'etapa_prenda' => 'required|string|in:' . implode(',', $this->stages),
            'orden_posiciones' => 'required|array',
            'orden_posiciones' => 'required|integer|min:1',
        ]);

        $uploadedImages = [];

        try {
            foreach ($request->file('images') as $index => $file) {
                $description = $request->descripciones[$index] ?? '';
                $orderPosition = $request->orden_posiciones[$index] ?? 1;

                //Generar nombres unicos
                $timestamp = now()->format('YmdHis');
                $random = substr(md5(uniqid()), 0, 8);
                $extension = $file->getClientOriginalExtension();

                //Rutas de almacenamiento
                $filename = "img_{$timestamp}_{$random}";
                $thumbnailPath = "thumbnails/{$filename}_thumb.{$extension}";
                $fullSizePath = "images/{$filename}.{$extension}";

                //Crear directorios si no existen
                Storage::disk('public')->makeDirectory('images');
                Storage::disk('public')->makeDirectory('thumbnails');

                //Procesar imagen completa (redimensionar si es muy grande)
                $fullImage = InterventionImage::make($file);
                if ($fullImage->width() > 1920 || $fullImage->height() > 1080) {
                    $fullImage->resize(1920, 1080, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    });
                }

                //Guardar imagen completa
                Storage::disk('public')->put($fullSizePath, $fullImage->encode($extension, 85));

                //Crear thumbnail (300x300)
                $thumbnail = $fullImage->fit(300, 300);
                Storage::disk('public')->put($thumbnailPath, $thumbnail->encode($extension, 80));

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
                $uploadedImages[] = $image;
            }

            return response()->json([
                'success' => true,
                'message' => 'imagenes guardadas exitosamente',
                'images' => $uploadedImages,
                'count' => count($uploadedImages)
            ]);
        } catch (\Exception $e) {
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
        $request->validate([
            'descripcion' => 'required|string|max:255',
            'orden_posicion' => 'required|integer|min:1',
            'etapa_prenda' => 'required|string|in:' . implode(',', $this->stages),
        ]);

        $image->update($request->only(['descripcion', 'orden_posicion', 'etapa_prenda']));

        return response()->json([
            'success' => true,
            'message' => 'Imagen actualizada exitosamente',
            'image' => $image
        ]);
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
    // Funcion para Recortar imagen
    public function cropImage(Request $request)
    {
        $request->validate([
            'image_id' => 'required|exists:images,id',
            'x' => 'required|numeric',
            'y' => 'required|numeric',
            'width' => 'required|numeric',
            'height' => 'required|numeric',
            'rotate' => 'nullable|numeric',
            'scaleX' => 'nullable|numeric',
            'scaleY' => 'nullable|numeric',
        ]);

        $image = Image::findOrFail($request->image_id);

        try {
            // Cargar la imagen original
            $originalPath = storage_path('app/public/' . $image->full_size_path);
            $img = InterventionImage::make($originalPath);

            // Aplicar rotación si existe
            if ($request->rotate && $request->rotate != 0) {
                $img->rotate(-$request->rotate);
            }

            // Aplicar escala si existe
            if ($request->scaleX && $request->scaleX != 1) {
                $img->flip('h');
            }
            if ($request->scaleY && $request->scaleY != 1) {
                $img->flip('v');
            }

            // Aplicar recorte
            $img->crop(
                (int) $request->width,
                (int) $request->height,
                (int) $request->x,
                (int) $request->y
            );

            // Generar nuevos nombres de archivo
            $timestamp = now()->format('YmdHis');
            $random = substr(md5(uniqid()), 0, 8);
            $extension = pathinfo($image->full_size_path, PATHINFO_EXTENSION);

            $filename = "img_{$timestamp}_{$random}";
            $newThumbnailPath = "thumbnails/{$filename}_thumb.{$extension}";
            $newFullSizePath = "images/{$filename}.{$extension}";

            // Guardar imagen recortada
            Storage::disk('public')->put($newFullSizePath, $img->encode($extension, 85));

            // Crear nuevo thumbnail
            $thumbnail = $img->fit(300, 300);
            Storage::disk('public')->put($newThumbnailPath, $thumbnail->encode($extension, 80));

            // Eliminar archivos antiguos
            $image->deleteFiles();

            // Actualizar rutas en la base de datos
            $image->update([
                'tamanio_miniatura' => $newThumbnailPath,
                'tamanio_completo' => $newFullSizePath,
                'tamanio_del_archivo' => Storage::disk('public')->size($newFullSizePath),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Imagen recortada correctamente',
                'image' => $image->fresh(),
                'thumbnail_url' => $image->thumbnail_url,
                'full_size_url' => $image->full_size_url
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar la imagen: ' . $e->getMessage()
            ], 500);
        }
    }
}

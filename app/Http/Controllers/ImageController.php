<?php

namespace App\Http\Controllers;

use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image as InterventionImage;
use Illuminate\Support\Str;

class ImageController extends Controller
{
    public function index(Request $request)
    {
        $query = Image::query()->ordered();

        if ($request->has('stage') && $request->stage){
            $query->byStage($request->stage);
        }
        //Obtener conjunto de imagenes del resultado de consulta(consulta a DB de manera paginada)
        $images = $query->paginate(12);
        $stages = ['diseño', 'confección', 'acabado', 'control_calidad', 'empaque'];

        return view('images.index', compact('images', 'stages'));
    }
    // ===>>>CRUD<<<===
    public function create()
    {
        $stages = ['diseño', 'confección', 'acabado', 'control_calidad', 'empaque'];
        return view('images.create', compact('stages'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'images' => 'required|array',
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:10240',
            'descripciones' => 'required|array',
            'descripciones.*' => 'required|string|max:255',
            'etapa_prenda' => 'required|string',
            'orden_posicion' => 'array',
        ]);

        $savedImages = [];

        foreach ($request->file('images') as $index => $file){
            $description = $request->descriptions[$index] ?? '';
            $orderPosition = $request->order_positions[$index] ?? 0;

            //Generar nombres unicos
            $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
            $thumbnailFilename = 'thumb_' . $filename;

            //Rutas de almacenamiento
            $fullSizePath = 'images/full/' . $filename;
            $thumbnailPath = 'images/thumbnails/' . $thumbnailFilename;

            //Crear directorios si no existen
            Storage::disk('public')->makeDirectory('images/full');
            Storage::disk('public')->makeDirectory('images/thumbnails');

            //Procesar imagen completa (redimensionar si es muy grande)
            $fullImage = InterventionImage::make($file->getRealPath());
            if ($fullImage->width() > 1920 || $fullImage->height() > 1080) {
                $fullImage->resize(1920, 1080, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
            }
            $fullImage->save(storage_path('app/public/' . $fullSizePath), 85);

            //--> Crear miniatura
            $thumbnail = InterventionImage::make($file->getRealPath());
            $thumbnail->fit(300, 300);
            $thumbnail->save(storage_path('app/public/' . $thumbnailPath), 80);

            // Guardar en base de datos
            $savedImages[] = Image::create([
                'descripcion' => $description,
                'orden_posicion' => $orderPosition,
                'etapa_prenda' => $request->clothing_stage,
                'tamanio_miniatura' => $thumbnailPath,
                'tamanio_completo' => $fullSizePath,
                'nombre_original' => $file->getClientOriginalName(),
                'tamanio_del_archivo' => $file->getSize(),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => count($savedImages) . 'imagenes guardadas exitosamente',
            'images' => $savedImages
        ]);
    }

    public function show(Image $image)
    {
        return view('images.show', compact('image'));
    }

    public function edit(Image $image)
    {
        $stages = ['diseño', 'confección', 'acabado', 'control_calidad', 'empaque'];
        return view('images.edit', compact('image', 'stages'));
    }

    public function update(Request $request, Image $image)
    {
        $request->validate([
            'descripcion' => 'required|string|max:255',
            'orden_posicion' => 'required|integer|min:0',
            'etapa_prenda' => 'required|string',
        ]);

        $image->update($request->only(['descripcion', 'orden_posicion', 'etapa_prenda']));

        return response()->json([
            'success' => true,
            'message' => 'Imagen actualizada exitosamente'
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
            'images.*.orden_posicion' => 'required|integer|min:0',
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
    // Funcion Recortar imagen
    public function cropImage(Request $request)
    {
        $request->validate([
            'image_id' => 'required|exists:images,id',
            'x' => 'required|numeric',
            'y' => 'required|numeric',
            'width' => 'required|numeric',
            'height' => 'required|numeric',
        ]);

        $image = Image::findOrFail($request->image_id);

        // Procesar crop en la imagen completa
        $fullImage = InterventionImage::make(storage_path('app/public/' . $image->full_size_path));
        $croppedImage = $fullImage->crop(
            (int)$request->width,
            (int)$request->height,
            (int)$request->x,
            (int)$request->y
        );

        // Guardar imagen recortada
        $croppedImage->save(storage_path('app/public/' . $image->full_size_path), 85);

        // Actualizar miniatura
        $thumbnail = $croppedImage->copy();
        $thumbnail->fit(300, 300);
        $thumbnail->save(storage_path('app/public/' . $image->thumbnail_path), 80);

        return response()->json([
            'success' => true,
            'message' => 'Imagen recortada exitosamente'
        ]);
    }
}

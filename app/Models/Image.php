<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Image extends Model
{
    use HasFactory;

    protected $fillable = [
        'descripcion',
        'orden_posicion',
        'etapa_prenda',
        'tamanio_miniatura',
        'tamanio_completo',
        'nombre_original',
        'tamanio_del_archivo'
    ];

    protected $casts = [
        'orden_posicion' => 'integer',
        'tamanio_del_archivo' => 'integer',
    ];

    //Accessor para obtener la URL completa de la miniatura
    public function getThumbnailUrlAttribute()
    {
        return asset('storage/' .$this->tamanio_miniatura);
    }

    //Accessor para obtener la URL completa de la imagen grande
    public function getFullSizeUrlAttribute()
    {
        return asset('storage/' .$this->tamanio_completo);
    }

    //Scope para ordenar por posicion
    public function scopeOrdered($query)
    {
        return $query->orderBy('orden_posicion');
    }

    //Scope para filtrar por etapa
    public function scopeByStage($query, $stage)
    {
        return $query->where('etapa_prenda', $stage);
    }

    //Metodo para eliminar archivos fisicos
    public function deleteFiles()
    {
        Storage::disk('public')->delete($this->tamanio_miniatura);
        Storage::disk('public')->delete($this->tamanio_completo);
    }
}

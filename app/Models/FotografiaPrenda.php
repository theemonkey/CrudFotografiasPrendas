<?php
// filepath: app/Models/FotografiaPrenda.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class FotografiaPrenda extends Model
{
    use HasFactory;

    protected $table = 'fotografias_prendas';

    protected $fillable = [
        'orden_sit',
        'po',
        'oc',
        'descripcion',
        'tipo',
        'imagen_path',
        'imagen_original_name',
        'imagen_size',
        'imagen_mime_type',
        'fecha_subida',
        'subido_por',
        'metadatos'
    ];

    protected $casts = [
        'fecha_subida' => 'datetime',
        'metadatos' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Accessor para URL completa de la imagen
    public function getImagenUrlAttribute()
    {
        if ($this->imagen_path && Storage::disk('public')->exists($this->imagen_path)) {
            return Storage::disk('public')->url($this->imagen_path);
        }

        // Imagen por defecto si no existe
        return asset('images/default-product.jpg');
    }

    // Accessor para tamaño formateado
    public function getImagenSizeFormattedAttribute()
    {
        if (!$this->imagen_size) return 'N/A';

        $bytes = $this->imagen_size;
        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    // Scopes para filtrado
    public function scopePorOrdenSit($query, $ordenSit)
    {
        return $query->where('orden_sit', 'like', "%{$ordenSit}%");
    }

    public function scopePorPO($query, $po)
    {
        return $query->where('po', 'like', "%{$po}%");
    }

    public function scopePorOC($query, $oc)
    {
        return $query->where('oc', 'like', "%{$oc}%");
    }

    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    public function scopePorDescripcion($query, $descripcion)
    {
        return $query->where('descripcion', 'like', "%{$descripcion}%");
    }

    public function scopeEntreFechas($query, $fechaInicio, $fechaFin)
    {
        return $query->whereBetween('created_at', [$fechaInicio . ' 00:00:00', $fechaFin . ' 23:59:59']);
    }

    // Método para eliminar imagen física
    public function eliminarImagenFisica()
    {
        if ($this->imagen_path && Storage::disk('public')->exists($this->imagen_path)) {
            return Storage::disk('public')->delete($this->imagen_path);
        }
        return true;
    }

    // Event listeners
    protected static function boot()
    {
        parent::boot();

        // Al eliminar el registro, eliminar también la imagen física
        static::deleting(function ($fotografia) {
            $fotografia->eliminarImagenFisica();
        });
    }
}

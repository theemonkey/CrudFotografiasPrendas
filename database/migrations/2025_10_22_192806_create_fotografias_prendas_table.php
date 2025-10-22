<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fotografias_prendas', function (Blueprint $table) {
            $table->id();
            $table->string('orden_sit', 20)->nullable();
            $table->string('po', 20)->nullable();
            $table->string('oc', 20)->nullable();
            $table->string('descripcion')->nullable();
            $table->enum('tipo', ['MUESTRA', 'PRENDA FINAL', 'VALIDACION AC'])->nullable();
            $table->string('imagen_path')->nullable();    // Ruta de imagen almacenada
            $table->string('imagen_original_name')->nullable();  // Nombre original del archivo
            $table->unsignedBigInteger('imagen_size')->nullable();      // Tamaño en bytes
            $table->string('imagen_mime_type', 100)->nullable();  // image/jpeg, image/png, etc.
            $table->timestamp('fecha_subida')->useCurrent();
            $table->string('subido_por', 100)->nullable();  // Usuario que subió
            $table->json('metadatos')->nullable();                 // Información adicional
            $table->timestamps();

            // indices compuestos para optimizar búsquedas
            $table->index(['orden_sit', 'tipo']);
            $table->index(['po', 'tipo']);
            $table->index(['created_at', 'tipo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fotografias_prendas');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('images', function (Blueprint $table) {
            $table->id();
            $table->string('descripcion')->nullable();
            $table->integer('orden_posicion')->nullable();
            $table->string('etapa_prenda')->nullable();       // prenda, confección, acabado, etc.
            $table->string('tamanio_miniatura')->nullable();
            $table->string('tamanio_completo')->nullable();
            $table->string('nombre_original')->nullable();
            $table->integer('tamanio_del_archivo')->nullable();
            $table->timestamps();
        });

    }
     // Reverse the migrations.
    public function down(): void
    {
        Schema::droppIfExists('images');
    }
};

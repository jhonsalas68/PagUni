<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('periodos_inscripcion', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->string('periodo_academico', 10);
            $table->timestamp('fecha_inicio');
            $table->timestamp('fecha_fin');
            $table->boolean('activo')->default(true);
            $table->text('descripcion')->nullable();
            $table->timestamps();
            
            // Índices
            $table->index('activo');
            $table->index(['fecha_inicio', 'fecha_fin']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('periodos_inscripcion');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inscripciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('estudiante_id')->constrained('estudiantes')->onDelete('cascade');
            $table->foreignId('grupo_id')->constrained('grupos')->onDelete('cascade');
            $table->string('periodo_academico', 10);
            $table->timestamp('fecha_inscripcion');
            $table->enum('estado', ['activo', 'dado_de_baja', 'completado'])->default('activo');
            $table->timestamp('fecha_baja')->nullable();
            $table->text('motivo_baja')->nullable();
            $table->timestamps();
            
            // Índices
            $table->index('estudiante_id');
            $table->index('grupo_id');
            $table->index('periodo_academico');
            
            // Constraint único
            $table->unique(['estudiante_id', 'grupo_id', 'periodo_academico'], 'unique_inscripcion');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inscripciones');
    }
};

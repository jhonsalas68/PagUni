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
            $table->foreignId('materia_id')->constrained('materias')->onDelete('cascade');
            $table->foreignId('profesor_id')->constrained('profesores')->onDelete('cascade');
            $table->string('periodo', 10); // Ej: 2024-1, 2024-2
            $table->decimal('nota_final', 3, 2)->nullable();
            $table->enum('estado', ['inscrito', 'aprobado', 'reprobado', 'retirado'])->default('inscrito');
            $table->timestamps();
            
            $table->unique(['estudiante_id', 'materia_id', 'periodo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inscripciones');
    }
};
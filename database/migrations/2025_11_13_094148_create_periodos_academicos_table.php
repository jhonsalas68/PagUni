<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('periodos_academicos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 20)->unique(); // Ej: 2024-2, 2025-1
            $table->string('nombre', 100); // Ej: Segundo Semestre 2024
            $table->integer('anio'); // 2024, 2025
            $table->integer('semestre'); // 1 o 2
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->enum('estado', ['activo', 'inactivo', 'finalizado'])->default('activo');
            $table->boolean('es_actual')->default(false); // Solo uno puede ser actual
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('periodos_academicos');
    }
};

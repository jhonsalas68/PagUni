<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('calificaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inscripcion_id')->constrained('inscripciones')->onDelete('cascade');
            $table->foreignId('tipo_evaluacion_id')->constrained('tipos_evaluacion')->onDelete('cascade');
            $table->decimal('nota', 5, 2); // 0.00 a 20.00 (o 100.00)
            $table->date('fecha')->default(now());
            $table->text('observaciones')->nullable();
            $table->timestamps();

            // Evitar duplicados: una nota por tipo de evaluación por inscripción
            $table->unique(['inscripcion_id', 'tipo_evaluacion_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('calificaciones');
    }
};

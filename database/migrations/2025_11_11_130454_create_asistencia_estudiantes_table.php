<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asistencia_estudiantes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inscripcion_id')->constrained('inscripciones')->onDelete('cascade');
            $table->foreignId('horario_id')->constrained('horarios')->onDelete('cascade');
            $table->date('fecha');
            $table->timestamp('hora_registro');
            $table->enum('estado', ['presente', 'ausente', 'tardanza', 'justificado'])->default('ausente');
            $table->enum('metodo_registro', ['qr', 'manual', 'automatico'])->default('qr');
            $table->string('qr_token', 255)->nullable();
            $table->text('observaciones')->nullable();
            $table->text('justificacion')->nullable();
            $table->foreignId('registrado_por')->nullable()->constrained('profesores')->onDelete('set null');
            $table->decimal('latitud', 10, 8)->nullable();
            $table->decimal('longitud', 11, 8)->nullable();
            $table->timestamps();
            
            // Índices
            $table->index('inscripcion_id');
            $table->index('fecha');
            $table->index('estado');
            
            // Constraint único
            $table->unique(['inscripcion_id', 'horario_id', 'fecha'], 'unique_asistencia_estudiante');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asistencia_estudiantes');
    }
};

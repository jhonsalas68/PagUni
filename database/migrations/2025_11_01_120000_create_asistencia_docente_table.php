<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('asistencia_docente', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profesor_id')->constrained('profesores')->onDelete('cascade');
            $table->foreignId('horario_id')->constrained('horarios')->onDelete('cascade');
            $table->date('fecha');
            $table->time('hora_entrada')->nullable();
            $table->time('hora_salida')->nullable();
            $table->enum('estado', ['presente', 'ausente', 'tardanza', 'justificado', 'en_clase'])->default('ausente');
            $table->text('justificacion')->nullable();
            $table->enum('tipo_justificacion', ['medica', 'personal', 'academica', 'administrativa', 'otra'])->nullable();
            $table->foreignId('justificado_por')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('fecha_justificacion')->nullable();
            $table->json('metadata')->nullable(); // Para datos adicionales como geolocalización, IP, etc.
            $table->boolean('validado_en_horario')->default(false);
            $table->text('observaciones')->nullable();
            $table->timestamps();
            
            // Índices para optimizar consultas
            $table->index(['profesor_id', 'fecha']);
            $table->index(['horario_id', 'fecha']);
            $table->index(['fecha', 'estado']);
            
            // Constraint único para evitar duplicados
            $table->unique(['profesor_id', 'horario_id', 'fecha'], 'unique_asistencia_diaria');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asistencia_docente');
    }
};
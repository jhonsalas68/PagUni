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
        Schema::create('aulas', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_aula', 20)->unique(); // Ej: A101, LAB-B205, AUD-C301
            $table->string('nombre'); // Nombre descriptivo del aula
            $table->enum('tipo_aula', ['aula', 'laboratorio', 'auditorio', 'sala_conferencias', 'biblioteca']); 
            $table->string('edificio'); // Nombre del edificio
            $table->integer('piso'); // Piso donde está ubicada
            $table->integer('capacidad'); // Número máximo de estudiantes
            $table->text('descripcion')->nullable(); // Descripción adicional
            $table->json('equipamiento')->nullable(); // Equipos disponibles (proyector, computadoras, etc.)
            $table->enum('estado', ['disponible', 'ocupada', 'mantenimiento', 'fuera_servicio'])->default('disponible');
            $table->boolean('tiene_aire_acondicionado')->default(false);
            $table->boolean('tiene_proyector')->default(false);
            $table->boolean('tiene_computadoras')->default(false);
            $table->boolean('acceso_discapacitados')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aulas');
    }
};

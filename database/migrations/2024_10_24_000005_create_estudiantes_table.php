<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('estudiantes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('apellido');
            $table->string('email')->unique();
            $table->string('telefono')->nullable();
            $table->string('cedula', 20)->unique();
            $table->string('codigo_estudiante', 15)->unique();
            $table->date('fecha_nacimiento');
            $table->text('direccion')->nullable();
            $table->foreignId('carrera_id')->constrained('carreras')->onDelete('cascade');
            $table->integer('semestre_actual')->default(1);
            $table->enum('estado', ['activo', 'inactivo', 'graduado', 'retirado'])->default('activo');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('estudiantes');
    }
};
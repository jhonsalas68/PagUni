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
        Schema::create('carga_academica', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profesor_id')->constrained('profesores')->onDelete('cascade');
            $table->foreignId('grupo_id')->constrained('grupos')->onDelete('cascade');
            $table->string('periodo', 10); // 2024-1, 2024-2
            $table->enum('estado', ['asignado', 'cancelado'])->default('asignado');
            $table->timestamps();
            
            // Un profesor no puede tener el mismo grupo en el mismo período
            $table->unique(['profesor_id', 'grupo_id', 'periodo']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carga_academica');
    }
};

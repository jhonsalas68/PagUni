<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tipos_evaluacion', function (Blueprint $table) {
            $table->id();
            $table->string('nombre'); // Parcial 1, Final, etc.
            $table->integer('ponderacion')->default(0); // Porcentaje (0-100)
            $table->enum('estado', ['activo', 'inactivo'])->default('activo');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tipos_evaluacion');
    }
};

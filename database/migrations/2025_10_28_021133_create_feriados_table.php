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
        Schema::create('feriados', function (Blueprint $table) {
            $table->id();
            $table->date('fecha_inicio')->comment('Fecha de inicio del feriado');
            $table->date('fecha_fin')->nullable()->comment('Fecha de fin del feriado (para rangos)');
            $table->string('descripcion')->comment('Descripción del feriado/evento');
            $table->enum('tipo', ['feriado', 'receso', 'asueto'])->default('feriado')->comment('Tipo de día no laborable');
            $table->boolean('es_rango')->default(false)->comment('Indica si es un rango de fechas');
            $table->boolean('activo')->default(true)->comment('Estado del feriado');
            $table->text('observaciones')->nullable()->comment('Observaciones adicionales');
            $table->timestamps();
            
            // Índices para optimizar consultas
            $table->index(['fecha_inicio', 'fecha_fin']);
            $table->index('activo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feriados');
    }
};

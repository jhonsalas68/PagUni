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
        Schema::table('horarios', function (Blueprint $table) {
            // Eliminar la columna periodo antigua que está causando conflictos
            $table->dropColumn(['periodo', 'tipo_asignacion', 'estado', 'observaciones']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('horarios', function (Blueprint $table) {
            // Restaurar las columnas eliminadas
            $table->string('periodo', 10)->nullable();
            $table->enum('tipo_asignacion', ['manual', 'automatica'])->default('manual');
            $table->enum('estado', ['activo', 'cancelado'])->default('activo');
            $table->text('observaciones')->nullable();
        });
    }
};

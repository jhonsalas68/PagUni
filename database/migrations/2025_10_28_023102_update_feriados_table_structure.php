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
        Schema::table('feriados', function (Blueprint $table) {
            // Eliminar campos que no están en el nuevo diseño
            $table->dropColumn(['es_rango', 'observaciones']);
            
            // Modificar el campo tipo para que sea enum
            $table->dropColumn('tipo');
        });
        
        Schema::table('feriados', function (Blueprint $table) {
            // Agregar el campo tipo como enum
            $table->enum('tipo', ['feriado', 'receso', 'asueto'])->default('feriado')->after('descripcion');
            
            // Agregar índices para optimizar consultas
            $table->index(['fecha_inicio', 'fecha_fin'], 'idx_feriados_fechas');
            $table->index('activo', 'idx_feriados_activo');
            $table->index('tipo', 'idx_feriados_tipo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('feriados', function (Blueprint $table) {
            // Eliminar índices
            $table->dropIndex('idx_feriados_fechas');
            $table->dropIndex('idx_feriados_activo');
            $table->dropIndex('idx_feriados_tipo');
            
            // Eliminar el campo tipo enum
            $table->dropColumn('tipo');
        });
        
        Schema::table('feriados', function (Blueprint $table) {
            // Restaurar campos originales
            $table->string('tipo')->after('descripcion');
            $table->boolean('es_rango')->default(false)->after('tipo');
            $table->text('observaciones')->nullable()->after('activo');
        });
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Eliminar la restricción CHECK existente
        DB::statement('ALTER TABLE carga_academica DROP CONSTRAINT IF EXISTS carga_academica_estado_check');
        
        // Modificar la columna para permitir los 4 estados
        DB::statement("ALTER TABLE carga_academica ADD CONSTRAINT carga_academica_estado_check CHECK (estado IN ('asignado', 'pendiente', 'completado', 'cancelado'))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Volver a la restricción original
        DB::statement('ALTER TABLE carga_academica DROP CONSTRAINT IF EXISTS carga_academica_estado_check');
        DB::statement("ALTER TABLE carga_academica ADD CONSTRAINT carga_academica_estado_check CHECK (estado IN ('asignado', 'cancelado'))");
    }
};

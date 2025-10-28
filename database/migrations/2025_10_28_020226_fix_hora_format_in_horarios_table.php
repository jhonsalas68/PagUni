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
        // Limpiar los datos de hora que pueden estar en formato datetime (PostgreSQL)
        DB::statement("
            UPDATE horarios 
            SET hora_inicio = CAST(hora_inicio AS TIME), 
                hora_fin = CAST(hora_fin AS TIME)
            WHERE hora_inicio IS NOT NULL 
            AND hora_fin IS NOT NULL
        ");
        
        // Asegurar que las columnas sean de tipo TIME
        Schema::table('horarios', function (Blueprint $table) {
            $table->time('hora_inicio')->change();
            $table->time('hora_fin')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No hacer nada en el rollback
    }
};
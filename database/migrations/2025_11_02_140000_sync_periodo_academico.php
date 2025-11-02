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
        // Verificar si la columna periodo_academico existe en carga_academica
        if (!Schema::hasColumn('carga_academica', 'periodo_academico')) {
            Schema::table('carga_academica', function (Blueprint $table) {
                $table->string('periodo_academico', 20)->nullable()->after('periodo');
            });
        }

        // Sincronizar datos: copiar periodo a periodo_academico en carga_academica
        DB::statement("UPDATE carga_academica SET periodo_academico = periodo WHERE periodo_academico IS NULL");

        // Hacer que periodo_academico sea requerido
        Schema::table('carga_academica', function (Blueprint $table) {
            $table->string('periodo_academico', 20)->nullable(false)->change();
        });

        // Asegurar que horarios tenga valores por defecto para periodo_academico
        DB::statement("UPDATE horarios SET periodo_academico = '2025-2' WHERE periodo_academico IS NULL OR periodo_academico = ''");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('carga_academica', 'periodo_academico')) {
            Schema::table('carga_academica', function (Blueprint $table) {
                $table->dropColumn('periodo_academico');
            });
        }
    }
};
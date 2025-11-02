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
        // Eliminar la restricción única que impide múltiples sesiones por día
        Schema::table('asistencia_docente', function (Blueprint $table) {
            $table->dropUnique('unique_asistencia_diaria');
        });

        // Actualizar el enum del estado para incluir 'pendiente_qr'
        DB::statement("ALTER TABLE asistencia_docente DROP CONSTRAINT IF EXISTS asistencia_docente_estado_check");
        DB::statement("ALTER TABLE asistencia_docente ADD CONSTRAINT asistencia_docente_estado_check CHECK (estado IN ('presente', 'ausente', 'tardanza', 'justificado', 'en_clase', 'pendiente_qr'))");

        // Crear nueva restricción única que incluye numero_sesion
        Schema::table('asistencia_docente', function (Blueprint $table) {
            $table->unique(['profesor_id', 'horario_id', 'fecha', 'numero_sesion'], 'unique_asistencia_sesion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Eliminar la nueva restricción única
        Schema::table('asistencia_docente', function (Blueprint $table) {
            $table->dropUnique('unique_asistencia_sesion');
        });

        // Restaurar el enum original
        DB::statement("ALTER TABLE asistencia_docente DROP CONSTRAINT IF EXISTS asistencia_docente_estado_check");
        DB::statement("ALTER TABLE asistencia_docente ADD CONSTRAINT asistencia_docente_estado_check CHECK (estado IN ('presente', 'ausente', 'tardanza', 'justificado', 'en_clase'))");

        // Restaurar la restricción única original
        Schema::table('asistencia_docente', function (Blueprint $table) {
            $table->unique(['profesor_id', 'horario_id', 'fecha'], 'unique_asistencia_diaria');
        });
    }
};
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
            $table->string('periodo_academico', 20)->default('2024-2')->after('duracion_horas');
            $table->boolean('es_semestral')->default(true)->after('periodo_academico');
            $table->date('fecha_inicio')->nullable()->after('es_semestral');
            $table->date('fecha_fin')->nullable()->after('fecha_inicio');
            $table->integer('semanas_duracion')->default(16)->after('fecha_fin');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('horarios', function (Blueprint $table) {
            $table->dropColumn(['periodo_academico', 'es_semestral', 'fecha_inicio', 'fecha_fin', 'semanas_duracion']);
        });
    }
};

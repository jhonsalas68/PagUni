<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('horarios', function (Blueprint $table) {
            // Agregar campos para configuración por día
            $table->json('configuracion_dias')->nullable()->after('tipo_clase');
            $table->boolean('usar_configuracion_por_dia')->default(false)->after('configuracion_dias');
        });
    }

    public function down()
    {
        Schema::table('horarios', function (Blueprint $table) {
            $table->dropColumn(['configuracion_dias', 'usar_configuracion_por_dia']);
        });
    }
};
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
            // Cambiar dia_semana de enum a integer
            $table->dropColumn('dia_semana');
        });
        
        Schema::table('horarios', function (Blueprint $table) {
            $table->integer('dia_semana')->after('aula_id')->comment('1=Lunes, 2=Martes, 3=Miércoles, 4=Jueves, 5=Viernes, 6=Sábado, 7=Domingo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('horarios', function (Blueprint $table) {
            $table->dropColumn('dia_semana');
        });
        
        Schema::table('horarios', function (Blueprint $table) {
            $table->enum('dia_semana', ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado'])->after('aula_id');
        });
    }
};

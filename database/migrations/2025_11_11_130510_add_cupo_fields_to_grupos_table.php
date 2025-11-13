<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('grupos', function (Blueprint $table) {
            $table->integer('cupo_maximo')->default(30)->after('identificador');
            $table->integer('cupo_actual')->default(0)->after('cupo_maximo');
            $table->boolean('permite_inscripcion')->default(true)->after('cupo_actual');
        });
    }

    public function down(): void
    {
        Schema::table('grupos', function (Blueprint $table) {
            $table->dropColumn(['cupo_maximo', 'cupo_actual', 'permite_inscripcion']);
        });
    }
};

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
        Schema::table('grupos', function (Blueprint $table) {
            $table->string('periodo_academico', 20)->default('2025-2')->after('materia_id');
        });

        // Set default value for existing grupos
        DB::statement("UPDATE grupos SET periodo_academico = '2025-2' WHERE periodo_academico IS NULL OR periodo_academico = ''");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('grupos', function (Blueprint $table) {
            $table->dropColumn('periodo_academico');
        });
    }
};
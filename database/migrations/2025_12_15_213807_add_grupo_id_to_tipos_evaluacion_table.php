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
        Schema::table('tipos_evaluacion', function (Blueprint $table) {
            $table->foreignId('grupo_id')->nullable()->after('id')->constrained('grupos')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('tipos_evaluacion', function (Blueprint $table) {
            $table->dropForeign(['grupo_id']);
            $table->dropColumn('grupo_id');
        });
    }
};

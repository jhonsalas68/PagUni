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
        Schema::table('asistencia_docente', function (Blueprint $table) {
            // Campos para sistema QR
            $table->string('qr_token', 100)->nullable()->after('horario_id');
            $table->timestamp('qr_generado_at')->nullable()->after('qr_token');
            $table->timestamp('qr_escaneado_at')->nullable()->after('qr_generado_at');
            
            // Modalidad de la clase
            $table->enum('modalidad', ['presencial', 'virtual'])->default('presencial')->after('estado');
            
            // Número de sesión para clases repetidas
            $table->integer('numero_sesion')->default(1)->after('modalidad');
            
            // IP y ubicación del escaneo QR
            $table->string('ip_escaneo')->nullable()->after('metadata');
            $table->json('ubicacion_escaneo')->nullable()->after('ip_escaneo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('asistencia_docente', function (Blueprint $table) {
            $table->dropColumn([
                'qr_token',
                'qr_generado_at', 
                'qr_escaneado_at',
                'modalidad',
                'numero_sesion',
                'ip_escaneo',
                'ubicacion_escaneo'
            ]);
        });
    }
};
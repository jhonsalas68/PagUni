<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Agregar campos para estado en línea a usuarios
        Schema::table('profesores', function (Blueprint $table) {
            $table->timestamp('last_seen_at')->nullable();
            $table->boolean('is_online')->default(false);
        });

        Schema::table('estudiantes', function (Blueprint $table) {
            $table->timestamp('last_seen_at')->nullable();
            $table->boolean('is_online')->default(false);
        });

        Schema::table('administradores', function (Blueprint $table) {
            $table->timestamp('last_seen_at')->nullable();
            $table->boolean('is_online')->default(false);
        });

        // Mejorar tabla de conversaciones para grupos
        Schema::table('conversations', function (Blueprint $table) {
            $table->text('description')->nullable();
            $table->string('created_by_type')->nullable();
            $table->unsignedBigInteger('created_by_id')->nullable();
            $table->json('metadata')->nullable(); // Para información adicional
        });

        // Mejorar tabla de mensajes
        Schema::table('messages', function (Blueprint $table) {
            $table->string('message_type')->default('text'); // text, image, file, system
            $table->json('metadata')->nullable(); // Para información adicional del mensaje
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
        });

        // Agregar tabla para participantes con más información
        Schema::table('conversation_participants', function (Blueprint $table) {
            $table->string('role')->default('member'); // member, admin, moderator
            $table->boolean('notifications_enabled')->default(true);
            $table->timestamp('joined_at')->nullable();
        });

        // Crear tabla para tracking de usuarios en línea
        Schema::create('user_online_status', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('user_type');
            $table->timestamp('last_activity');
            $table->string('status')->default('online'); // online, away, busy, offline
            $table->timestamps();

            $table->index(['user_type', 'user_id']);
            $table->unique(['user_id', 'user_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_online_status');
        
        Schema::table('conversation_participants', function (Blueprint $table) {
            $table->dropColumn(['role', 'notifications_enabled', 'joined_at']);
        });

        Schema::table('messages', function (Blueprint $table) {
            $table->dropColumn(['message_type', 'metadata', 'is_read', 'read_at']);
        });

        Schema::table('conversations', function (Blueprint $table) {
            $table->dropColumn(['description', 'created_by_type', 'created_by_id', 'metadata']);
        });

        Schema::table('administradores', function (Blueprint $table) {
            $table->dropColumn(['last_seen_at', 'is_online']);
        });

        Schema::table('estudiantes', function (Blueprint $table) {
            $table->dropColumn(['last_seen_at', 'is_online']);
        });

        Schema::table('profesores', function (Blueprint $table) {
            $table->dropColumn(['last_seen_at', 'is_online']);
        });
    }
};
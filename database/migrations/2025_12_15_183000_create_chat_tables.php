<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Conversations Table
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->string('type')->default('private'); // private, group, announcement
            $table->foreignId('subject_id')->nullable()->constrained('materias')->onDelete('cascade'); // Optional: Link to a subject
            $table->string('title')->nullable(); // For group chats
            $table->timestamps();
        });

        // Participants Table (Polymorphic)
        Schema::create('conversation_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained('conversations')->onDelete('cascade');
            
            // Polymorphic relation for user (Student, Professor, Admin)
            $table->unsignedBigInteger('participant_id');
            $table->string('participant_type');
            
            $table->timestamp('last_read_at')->nullable();
            $table->timestamps();

            // Index for faster lookups
            $table->index(['participant_type', 'participant_id']);
            $table->unique(['conversation_id', 'participant_id', 'participant_type'], 'conv_part_unique');
        });

        // Messages Table (Polymorphic Sender)
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained('conversations')->onDelete('cascade');
            
            // Polymorphic relation for sender
            $table->unsignedBigInteger('sender_id');
            $table->string('sender_type');
            
            $table->text('content');
            $table->timestamps();

            // Index
            $table->index(['sender_type', 'sender_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');
        Schema::dropIfExists('conversation_participants');
        Schema::dropIfExists('conversations');
    }
};

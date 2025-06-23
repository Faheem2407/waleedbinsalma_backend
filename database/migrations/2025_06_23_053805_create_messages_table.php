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
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sender_id')->constrained('users');
            $table->foreignId('receiver_id')->constrained('users');
            $table->foreignId('conversation_id')->constrained('conversations');
            $table->text('message')->nullable();
            $table->string('file_name')->nullable();
            $table->string('original_name')->nullable();
            $table->string('file_path')->nullable();
            $table->string('file_type')->nullable();
            $table->enum('type', ['text', 'file', 'voice'])->default('text');
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};

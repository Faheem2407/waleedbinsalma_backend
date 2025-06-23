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
        Schema::create('online_stores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_profile_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('about');
            $table->string('phone');
            $table->string('email');
            $table->string('address');
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('online_stores');
    }
};

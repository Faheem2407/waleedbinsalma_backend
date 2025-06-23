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
        Schema::create('opening_hours', function (Blueprint $table) {
            $table->id();
            $table->foreignId('online_store_id')->constrained()->onDelete('cascade');
            $table->string('day_name');
            $table->string('morning_start_time')->nullable();
            $table->string('morning_end_time')->nullable();
            $table->string('evening_start_time')->nullable();
            $table->string('evening_end_time')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('opening_hours');
    }
};

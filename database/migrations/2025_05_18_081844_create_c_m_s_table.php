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
        Schema::create('c_m_s', function (Blueprint $table) {
            $table->id();
            $table->string('page');
            $table->string('section');
            $table->string('title');
            $table->string('sub_title')->nullable();
            $table->text('description')->nullable();
            $table->text('short_description')->nullable();
            $table->string('satisfied_clients')->nullable();
            $table->string('pro_consultants')->nullable();
            $table->string('years_in_businesses')->nullable();
            $table->string('successful_cases')->nullable();
            $table->string('file_url')->nullable();
            $table->string('background_image')->nullable();
            $table->string('icon')->nullable();
            $table->string('button_text')->nullable();
            $table->string('button_link')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active'); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cms');
    }
};

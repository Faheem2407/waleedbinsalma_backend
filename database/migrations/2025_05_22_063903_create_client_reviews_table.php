<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('client_reviews', function (Blueprint $table) {
            $table->id();
            $table->string('client_avatar')->nullable();
            $table->text('review');
            $table->unsignedTinyInteger('rating');
            $table->string('shop_name');
            $table->string('shop_location');
            $table->enum('status',['active','inactive'])->default('active');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_reviews');
    }
};

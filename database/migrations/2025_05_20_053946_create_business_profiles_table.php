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
        Schema::create('business_profiles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')
                    ->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('business_name')->nullable();
            $table->string('website_url')->nullable();
            $table->string('team_size')->nullable();
            $table->string('address')->nullable();
            $table->string('longitude')->nullable();
            $table->string('latitude')->nullable();
            $table->boolean('do_not_business_adders')->nullable();
            $table->string('calendly')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_profiles');
    }
};

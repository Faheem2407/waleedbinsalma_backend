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
        Schema::create('business_services', function (Blueprint $table) {
            $table->id();
             $table->unsignedBigInteger('business_profile_id');
            $table->foreign('business_profile_id')->references('id')->on('business_profiles')
                    ->cascadeOnUpdate()->cascadeOnDelete();
            $table->unsignedBigInteger('service_id');
            $table->foreign('service_id')->references('id')->on('services')
                    ->cascadeOnUpdate()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_services');
    }
};

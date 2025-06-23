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
        Schema::create('catalog_services', function (Blueprint $table) {
            $table->id();
            $table->foreign('catalog_service_category_id')->references('id')->on('catalog_service_categories')
                ->cascadeOnUpdate()->cascadeOnDelete();
            $table->unsignedBigInteger('catalog_service_category_id');
            $table->foreign('business_profile_id')->references('id')->on('business_profiles')
                ->cascadeOnUpdate()->cascadeOnDelete();
            $table->unsignedBigInteger('business_profile_id');
            $table->foreign('service_id')->references('id')->on('services')
                ->cascadeOnUpdate()->cascadeOnDelete();
            $table->unsignedBigInteger('service_id');
            $table->string('name');
            $table->string('description');
            $table->string('duration');
            $table->string('price_type');
            $table->string('price');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('catalog_services');
    }
};

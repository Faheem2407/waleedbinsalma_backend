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
        Schema::create('team_services', function (Blueprint $table) {
            $table->id();
            $table->foreign('team_id')->references('id')->on('teams')
                ->cascadeOnUpdate()->cascadeOnDelete();
            $table->unsignedBigInteger('team_id');
            $table->foreign('catalog_service_id')->references('id')->on('catalog_services')
                ->cascadeOnUpdate()->cascadeOnDelete();
            $table->unsignedBigInteger('catalog_service_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('team_services');
    }
};

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
        Schema::create('team_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreign('team_id')->references('id')->on('teams')
                ->cascadeOnUpdate()->cascadeOnDelete();
            $table->unsignedBigInteger('team_id');
            $table->string('address_name');
            $table->string('address');
            $table->string('apt_suite')->nullable();
            $table->string('district')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            $table->string('state')->nullable();
            $table->string('post_code')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('team_addresses');
    }
};

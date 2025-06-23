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
        Schema::create('business_documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('business_profile_id');
            $table->foreign('business_profile_id')->references('id')->on('business_profiles')
                    ->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('trade_license')->nullable();
            $table->string('vat_registration_certificate')->nullable();
            $table->string('passport_copy')->nullable();
            $table->string('account_statement')->nullable();
            $table->boolean('terms_and_condition')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_documents');
    }
};

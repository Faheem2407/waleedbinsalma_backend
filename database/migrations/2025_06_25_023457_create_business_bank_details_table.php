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
        Schema::create('business_bank_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_profile_id')->cascadeOnUpdate()
                ->constrained('business_profiles')->cascadeOnDelete();
            $table->string('stripe_account_id')->nullable();
            $table->enum('status', ['NotTry', 'Enabled', 'Rejected'])->default('NotTry');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_bank_details');
    }
};

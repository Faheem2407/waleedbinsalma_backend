<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('discount_codes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('online_store_id')->constrained()->onDelete('cascade');
            $table->string('code')->unique();
            $table->decimal('discount_amount', 8, 2)->nullable(); // For fixed amount discounts
            $table->integer('discount_percentage')->nullable(); // For percentage-based discounts
            $table->date('valid_from')->nullable();
            $table->date('valid_until')->nullable();
            $table->integer('usage_limit')->nullable(); // Maximum number of uses
            $table->integer('used_count')->default(0); // Track usage
            $table->boolean('is_active')->default(true);
            $table->decimal('minimum_amount', 8, 2)->nullable(); // Minimum order amount for discount
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('discount_codes');
    }
};
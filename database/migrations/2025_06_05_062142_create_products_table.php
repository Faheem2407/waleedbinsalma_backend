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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_profile_id')
                ->constrained('business_profiles')
                ->onDelete('cascade');
            $table->foreignId('category_id')
                ->constrained('product_categories')
                ->onDelete('cascade');
            $table->foreignId('brand_id')
                ->constrained('product_brands')
                ->onDelete('cascade');
            $table->string('name');
            $table->string('barcode')->nullable();
            $table->string('measure');
            $table->double('amount')->default(0);
            $table->text('short_description')->nullable();
            $table->text('description')->nullable();
            $table->double('supply_price')->default(0);
            $table->double('price')->default(0);
            $table->integer('stock_quantity')->default(0);
            $table->string('image_url')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};

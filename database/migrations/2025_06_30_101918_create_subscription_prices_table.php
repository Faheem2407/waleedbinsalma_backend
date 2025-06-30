<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscriptionPricesTable extends Migration
{
    public function up()
    {
        Schema::create('subscription_prices', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->decimal('price',8,2);
            $table->timestamps();
        });

        DB::table('subscription_prices')->insert([
            'name' => 'monthly subscription',
            'price' => 9.99,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('subscription_prices');
    }
}


<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('appointment_id')->nullable()->constrained()->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->string('currency')->default('usd');
            $table->enum('status', ['pending', 'succeeded', 'failed'])->default('pending');
            $table->string('payment_method')->nullable();
            $table->string('payment_intent_id')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('payments');
    }
};

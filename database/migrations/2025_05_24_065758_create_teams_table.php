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
        Schema::create('teams', function (Blueprint $table) {
            $table->id();
            $table->foreign('business_profile_id')->references('id')->on('business_profiles')
                ->cascadeOnUpdate()->cascadeOnDelete();
            $table->unsignedBigInteger('business_profile_id');
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->string('email');
            $table->string('phone');
            $table->string('country');
            $table->string('birthday');
            $table->string('job_title');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->enum('employment_type', ['full_time', 'part_time', 'contract', 'internship', 'volunteer']);
            $table->string('employee_id');
            $table->string('photo')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teams');
    }
};

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
        Schema::create('training_plans', function (Blueprint $table) {
            $table->id();
            $table->string('file_name');
            $table->string('files');
            $table->string('location');
            $table->string('department');
            $table->string('training_type');
            $table->string('labour_type');
            $table->dateTime('training_date');
            $table->dateTime('expired_date');
            $table->integer('training_hours');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('training_plans');
    }
};

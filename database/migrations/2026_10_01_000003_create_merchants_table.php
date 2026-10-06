<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('merchants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('business_name');
            $table->text('description')->nullable();
            $table->string('phone', 30);
            $table->text('address');
            $table->string('area', 50)->index();            // untuk filter lokasi (bukan GPS)
            $table->string('maps_url', 500)->nullable();
            $table->string('operating_hours')->nullable();
            $table->string('logo')->nullable();
            $table->string('verification_status', 20)->default('pending')->index();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('merchants');
    }
};

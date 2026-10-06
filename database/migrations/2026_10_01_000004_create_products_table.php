<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('merchant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained()->restrictOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->unsignedInteger('normal_price');          // Rupiah, tanpa desimal
            $table->unsignedInteger('foodsave_price');
            $table->unsignedTinyInteger('discount_percentage')->default(0); // dihitung otomatis di model
            $table->unsignedInteger('stock')->default(0);
            $table->dateTime('pickup_start');
            $table->dateTime('pickup_end');
            $table->string('status', 20)->default('active');  // active | inactive
            $table->timestamps();
            $table->softDeletes();                            // produk yang pernah dijual tidak boleh hilang

            $table->index(['status', 'pickup_end']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};

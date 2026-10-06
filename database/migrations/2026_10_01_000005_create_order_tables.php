<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', 30)->unique();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->foreignId('merchant_id')->constrained()->restrictOnDelete();
            $table->unsignedInteger('subtotal');
            $table->decimal('service_fee_percent', 5, 2)->default(0); // snapshot rate saat transaksi
            $table->unsignedInteger('service_fee')->default(0);       // kewajiban MITRA, bukan tambahan biaya konsumen
            $table->unsignedInteger('total');                          // = subtotal (dibayar langsung ke mitra)
            $table->string('status', 30)->index();
            $table->timestamp('ordered_at');
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->restrictOnDelete();
            $table->string('product_name_snapshot');
            $table->unsignedInteger('unit_price');                     // snapshot harga
            $table->unsignedSmallInteger('quantity');
            $table->unsignedInteger('subtotal');
            $table->timestamps();
        });

        Schema::create('order_status_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('status', 30);
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('note')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        // Diisi pada Fase 7 saat order Completed (PRD FR-11). Tabel disiapkan sekarang
        // supaya relasi "setiap fee terhubung ke transaksi" sudah dijaga constraint DB.
        Schema::create('service_fees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->unique()->constrained()->restrictOnDelete();
            $table->foreignId('merchant_id')->constrained()->restrictOnDelete();
            $table->unsignedBigInteger('settlement_id')->nullable()->index(); // FK ditambahkan saat tabel settlements dibuat
            $table->decimal('fee_percentage', 5, 2);
            $table->unsignedInteger('transaction_amount');
            $table->unsignedInteger('fee_amount');
            $table->string('status', 20)->default('unsettled');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_fees');
        Schema::dropIfExists('order_status_histories');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};

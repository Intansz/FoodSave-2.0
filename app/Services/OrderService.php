<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Exceptions\OrderException;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class OrderService
{
    /**
     * Membuat order untuk satu produk (satu order = satu mitra).
     *
     * Semua langkah ada di SATU transaksi database dan baris produk dikunci
     * (SELECT ... FOR UPDATE) sehingga dua request bersamaan tidak bisa
     * sama-sama mengambil stok terakhir (PRD FR-07, FR-10, §8).
     *
     * @throws OrderException
     */
    public function place(User $user, Product $product, int $quantity): Order
    {
        if ($quantity < 1 || $quantity > 20) {
            throw ValidationException::withMessages([
                'quantity' => 'Jumlah pesanan harus antara 1 dan 20.',
            ]);
        }
        return DB::transaction(function () use ($user, $product, $quantity) {
            /** @var Product $locked */
            $locked = Product::query()
                ->whereKey($product->getKey())
                ->with('merchant')
                ->lockForUpdate()
                ->firstOrFail();

            if (! $locked->isAvailable()) {
                throw OrderException::unavailable();
            }

            if ($locked->stock < $quantity) {
                throw OrderException::insufficientStock($locked->stock);
            }

            $locked->decrement('stock', $quantity);

            // Harga & fee di-snapshot: perubahan setelahnya tidak memengaruhi order lama.
            $subtotal = $locked->foodsave_price * $quantity;
            $feePercent = (float) config('foodsave.service_fee_percent');
            $serviceFee = (int) round($subtotal * $feePercent / 100);

            $order = Order::create([
                'order_number' => $this->generateOrderNumber(),
                'user_id' => $user->id,
                'merchant_id' => $locked->merchant_id,
                'subtotal' => $subtotal,
                'service_fee_percent' => $feePercent,
                'service_fee' => $serviceFee,
                'total' => $subtotal,
                'status' => OrderStatus::Pending,
                'ordered_at' => now(),
            ]);

            $order->items()->create([
                'product_id' => $locked->id,
                'product_name_snapshot' => $locked->name,
                'unit_price' => $locked->foodsave_price,
                'quantity' => $quantity,
                'subtotal' => $subtotal,
            ]);

            $order->histories()->create([
                'status' => OrderStatus::Pending,
                'changed_by' => $user->id,
                'note' => 'Pesanan dibuat oleh konsumen.',
            ]);

            return $order;
        });
    }

    private function generateOrderNumber(): string
    {
        do {
            $number = 'FS-' . now()->format('ymd') . '-' . Str::upper(Str::random(5));
        } while (Order::where('order_number', $number)->exists());

        return $number;
    }
}

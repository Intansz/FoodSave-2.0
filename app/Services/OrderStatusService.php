<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Models\Product;
use App\Models\ServiceFee;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderStatusService
{
    public function updateByMerchant(
        Order $order,
        OrderStatus $nextStatus,
        User $actor
    ): Order {
        return $this->apply(
            $order,
            $nextStatus,
            $actor,
            true
        );
    }

    public function cancelByAdmin(
        Order $order,
        User $actor
    ): Order {
        return $this->apply(
            $order,
            OrderStatus::Cancelled,
            $actor,
            false
        );
    }

    private function apply(
        Order $order,
        OrderStatus $nextStatus,
        User $actor,
        bool $byMerchant
    ): Order {
        return DB::transaction(function () use (
            $order,
            $nextStatus,
            $actor,
            $byMerchant
        ) {
            $locked = Order::query()
                ->whereKey($order->id)
                ->lockForUpdate()
                ->firstOrFail();

            // Validasi berdasarkan status terbaru di database.
            if ($byMerchant) {
                $allowed = match ($locked->status) {
                    OrderStatus::Pending => [
                        OrderStatus::Confirmed,
                        OrderStatus::Cancelled,
                    ],
                    OrderStatus::Confirmed => [
                        OrderStatus::ReadyForPickup,
                    ],
                    OrderStatus::ReadyForPickup => [
                        OrderStatus::Completed,
                    ],
                    default => [],
                };

                if (! in_array($nextStatus, $allowed, true)) {
                    throw ValidationException::withMessages([
                        'status' => 'Perubahan status pesanan tidak diizinkan. Muat ulang halaman dan coba lagi.',
                    ]);
                }
            } else {
                if (in_array($locked->status, [
                    OrderStatus::Completed,
                    OrderStatus::Cancelled,
                ], true)) {
                    throw ValidationException::withMessages([
                        'order' => 'Pesanan selesai atau sudah dibatalkan.',
                    ]);
                }
            }

            if ($nextStatus === OrderStatus::Cancelled) {
                $this->restoreStock($locked);
                $locked->cancelled_at = now();
            }

            if ($nextStatus === OrderStatus::Completed) {
                $locked->completed_at = now();
            }

            $locked->status = $nextStatus;
            $locked->save();

            OrderStatusHistory::create([
                'order_id' => $locked->id,
                'status' => $nextStatus,
                'changed_by' => $actor->id,
                'note' => $nextStatus === OrderStatus::Cancelled
                    ? 'Pesanan dibatalkan.'
                    : 'Status pesanan diperbarui.',
            ]);

            if ($nextStatus === OrderStatus::Completed) {
                ServiceFee::firstOrCreate(
                    ['order_id' => $locked->id],
                    [
                        'merchant_id' => $locked->merchant_id,
                        'fee_percentage' => $locked->service_fee_percent,
                        'transaction_amount' => $locked->subtotal,
                        'fee_amount' => $locked->service_fee,
                        'status' => 'unsettled',
                    ]
                );
            }

            return $locked->fresh([
                'user',
                'items',
                'merchant',
            ]);
        });
    }

    private function restoreStock(Order $order): void
    {
        foreach ($order->items as $item) {
            $product = Product::withTrashed()
                ->whereKey($item->product_id)
                ->lockForUpdate()
                ->first();

            if ($product) {
                $product->increment('stock', $item->quantity);
            }
        }
    }
}

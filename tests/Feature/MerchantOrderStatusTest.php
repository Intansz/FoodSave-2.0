<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Enums\UserRole;
use App\Models\User;
use App\Notifications\OrderStatusUpdated;
use App\Services\OrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\Support\BuildsCatalog;
use Tests\TestCase;

class MerchantOrderStatusTest extends TestCase
{
    use BuildsCatalog, RefreshDatabase;

    public function test_merchant_can_confirm_pending_order_and_notify_consumer(): void
    {
        Notification::fake();

        $product = $this->makeProduct();
        $consumer = $this->makeConsumer();
        $merchant = $product->merchant->user;

        $order = app(OrderService::class)->place(
            $consumer,
            $product,
            1
        );

        $this->actingAs($merchant)
            ->patch(route('merchant.orders.status', $order->id), [
                'status' => 'confirmed',
            ])
            ->assertRedirect(route('merchant.dashboard'))
            ->assertSessionHas(
                'status',
                'Status pesanan berhasil diperbarui.'
            );

        $this->assertSame(
            OrderStatus::Confirmed,
            $order->fresh()->status
        );

        Notification::assertSentTo(
            $consumer,
            OrderStatusUpdated::class,
            function (OrderStatusUpdated $notification) use ($order) {
                return $notification->order->id === $order->id
                    && $notification->order->status === OrderStatus::Confirmed;
            }
        );
    }

    public function test_merchant_cannot_update_another_merchants_order(): void
    {
        $productA = $this->makeProduct();
        $productB = $this->makeProduct();
        $consumer = $this->makeConsumer();

        $order = app(OrderService::class)->place(
            $consumer,
            $productA,
            1
        );

        $merchantB = $productB->merchant->user;

        $this->actingAs($merchantB)
            ->patch(route('merchant.orders.status', $order->id), [
                'status' => 'confirmed',
            ])
            ->assertNotFound();

        $this->assertSame(
            OrderStatus::Pending,
            $order->fresh()->status
        );
    }

    public function test_merchant_cannot_skip_order_statuses(): void
    {
        $product = $this->makeProduct();
        $consumer = $this->makeConsumer();
        $merchant = $product->merchant->user;

        $order = app(OrderService::class)->place(
            $consumer,
            $product,
            1
        );

        $this->actingAs($merchant)
            ->patch(route('merchant.orders.status', $order->id), [
                'status' => 'completed',
            ])
            ->assertRedirect()
            ->assertSessionHasErrors('status');

        $this->assertSame(
            OrderStatus::Pending,
            $order->fresh()->status
        );
    }

    public function test_merchant_cannot_cancel_order_after_confirmation(): void
    {
        $product = $this->makeProduct();
        $consumer = $this->makeConsumer();
        $merchant = $product->merchant->user;

        $order = app(OrderService::class)->place(
            $consumer,
            $product,
            1
        );

        // Konfirmasi pesanan terlebih dahulu.
        $this->actingAs($merchant)
            ->patch(route('merchant.orders.status', $order->id), [
                'status' => 'confirmed',
            ])
            ->assertRedirect(route('merchant.dashboard'));

        $stockAfterOrder = $product->fresh()->stock;

        // Percobaan membatalkan pesanan yang sudah dikonfirmasi harus ditolak.
        $this->actingAs($merchant)
            ->patch(route('merchant.orders.status', $order->id), [
                'status' => 'cancelled',
            ])
            ->assertRedirect()
            ->assertSessionHasErrors('status');

        $this->assertSame(
            OrderStatus::Confirmed,
            $order->fresh()->status
        );

        // Stok tidak boleh berubah akibat percobaan pembatalan yang ditolak.
        $this->assertSame(
            $stockAfterOrder,
            $product->fresh()->stock
        );

        // Service fee belum boleh tercatat sebelum pesanan selesai.
        $this->assertDatabaseMissing('service_fees', [
            'order_id' => $order->id,
        ]);
    }
}

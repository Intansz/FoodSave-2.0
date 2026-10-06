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

class AdminOrderCancellationTest extends TestCase
{
    use BuildsCatalog, RefreshDatabase;

    public function test_admin_can_cancel_confirmed_order_and_notify_consumer(): void
    {
        Notification::fake();

        $product = $this->makeProduct(['stock' => 3]);
        $consumer = $this->makeConsumer();
        $merchantUser = $product->merchant->user;

        $admin = User::create([
            'name' => 'Admin Uji',
            'email' => 'admin-' . uniqid() . '@test.test',
            'password' => 'password',
        ]);

        $admin->forceFill([
            'role' => UserRole::Admin,
        ])->save();

        $order = app(OrderService::class)->place(
            $consumer,
            $product,
            2
        );

        // Mitra mengonfirmasi pesanan melalui service.
        app(\App\Services\OrderStatusService::class)->updateByMerchant(
            $order,
            OrderStatus::Confirmed,
            $merchantUser
        );

        $this->actingAs($admin)
            ->patch(route('admin.orders.cancel', $order->id))
            ->assertRedirect()
            ->assertSessionHas(
                'status',
                'Pesanan berhasil dibatalkan oleh admin.'
            );

        $this->assertSame(
            OrderStatus::Cancelled,
            $order->fresh()->status
        );

        $this->assertNotNull($order->fresh()->cancelled_at);
        $this->assertSame(3, $product->fresh()->stock);

        $this->assertDatabaseHas('order_status_histories', [
            'order_id' => $order->id,
            'status' => OrderStatus::Cancelled->value,
            'changed_by' => $admin->id,
        ]);

        Notification::assertSentTo(
            $consumer,
            OrderStatusUpdated::class,
            function (OrderStatusUpdated $notification) use ($order) {
                return $notification->order->id === $order->id
                    && $notification->order->status === OrderStatus::Cancelled;
            }
        );
    }

    public function test_consumer_cannot_access_admin_cancellation_endpoint(): void
    {
        $product = $this->makeProduct();
        $consumer = $this->makeConsumer();

        $order = app(OrderService::class)->place(
            $consumer,
            $product,
            1
        );

        $this->actingAs($consumer)
            ->patch(route('admin.orders.cancel', $order->id))
            ->assertForbidden();

        $this->assertSame(
            OrderStatus::Pending,
            $order->fresh()->status
        );
    }
}

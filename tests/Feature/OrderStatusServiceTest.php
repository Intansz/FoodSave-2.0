<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Services\OrderService;
use App\Services\OrderStatusService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\Support\BuildsCatalog;
use Tests\TestCase;

class OrderStatusServiceTest extends TestCase
{
    use BuildsCatalog, RefreshDatabase;

    public function test_merchant_can_advance_order_through_valid_statuses(): void
    {
        $product = $this->makeProduct();
        $consumer = $this->makeConsumer();
        $merchantUser = $product->merchant->user;

        $order = app(OrderService::class)->place($consumer, $product, 1);
        $this->assertDatabaseMissing('service_fees', [
            'order_id' => $order->id,
        ]);
        $service = app(OrderStatusService::class);

        $service->updateByMerchant(
            $order,
            OrderStatus::Confirmed,
            $merchantUser
        );
        $this->assertDatabaseMissing('service_fees', [
            'order_id' => $order->id,
        ]);

        $order = $order->fresh();

        $service->updateByMerchant(
            $order,
            OrderStatus::ReadyForPickup,
            $merchantUser
        );

        $order = $order->fresh();

        $service->updateByMerchant(
            $order,
            OrderStatus::Completed,
            $merchantUser
        );

        $this->assertSame(OrderStatus::Completed, $order->fresh()->status);
        $this->assertNotNull($order->fresh()->completed_at);
        $this->assertDatabaseHas('service_fees', [
            'order_id' => $order->id,
            'status' => 'unsettled',
        ]);
        $this->assertSame(
            1,
            \App\Models\ServiceFee::where('order_id', $order->id)->count()
        );
        $this->assertSame(4, $order->histories()->count());
    }

    public function test_merchant_cannot_skip_order_statuses(): void
    {
        $product = $this->makeProduct();
        $consumer = $this->makeConsumer();
        $merchantUser = $product->merchant->user;

        $order = app(OrderService::class)->place($consumer, $product, 1);

        try {
            app(OrderStatusService::class)->updateByMerchant(
                $order,
                OrderStatus::Completed,
                $merchantUser
            );

            $this->fail('Pesanan tidak boleh langsung diselesaikan.');
        } catch (ValidationException) {
            $this->assertSame(
                OrderStatus::Pending,
                $order->fresh()->status
            );
        }
    }

    public function test_cancelling_pending_order_restores_stock_once(): void
    {
        $product = $this->makeProduct(['stock' => 3]);
        $consumer = $this->makeConsumer();
        $merchantUser = $product->merchant->user;

        $order = app(OrderService::class)->place($consumer, $product, 2);

        $this->assertSame(1, $product->fresh()->stock);

        app(OrderStatusService::class)->updateByMerchant(
            $order,
            OrderStatus::Cancelled,
            $merchantUser
        );

        $this->assertSame(3, $product->fresh()->stock);
        $this->assertSame(
            OrderStatus::Cancelled,
            $order->fresh()->status
        );
        $this->assertNotNull($order->fresh()->cancelled_at);
        $this->assertSame(2, $order->histories()->count());
    }

    public function test_admin_can_cancel_confirmed_order_and_restore_stock(): void
    {
        $product = $this->makeProduct(['stock' => 3]);
        $consumer = $this->makeConsumer();
        $merchantUser = $product->merchant->user;
        $admin = \App\Models\User::create([
            'name' => 'Admin Uji',
            'email' => 'admin-uji@test.test',
            'password' => 'password',
        ]);

        $admin->forceFill([
            'role' => \App\Enums\UserRole::Admin,
        ])->save();

        $order = app(OrderService::class)->place($consumer, $product, 2);
        $service = app(OrderStatusService::class);

        $service->updateByMerchant(
            $order,
            OrderStatus::Confirmed,
            $merchantUser
        );

        $service->cancelByAdmin($order->fresh(), $admin);

        $this->assertSame(3, $product->fresh()->stock);
        $this->assertSame(
            OrderStatus::Cancelled,
            $order->fresh()->status
        );
    }

    public function test_completed_order_cannot_be_cancelled_again(): void
    {
        $product = $this->makeProduct();
        $consumer = $this->makeConsumer();
        $merchantUser = $product->merchant->user;
        $admin = \App\Models\User::create([
            'name' => 'Admin Uji',
            'email' => 'admin-uji@test.test',
            'password' => 'password',
        ]);

        $admin->forceFill([
            'role' => \App\Enums\UserRole::Admin,
        ])->save();

        $service = app(OrderStatusService::class);
        $order = app(OrderService::class)->place($consumer, $product, 1);

        $service->updateByMerchant($order, OrderStatus::Confirmed, $merchantUser);
        $order = $order->fresh();

        $service->updateByMerchant($order, OrderStatus::ReadyForPickup, $merchantUser);
        $order = $order->fresh();

        $service->updateByMerchant($order, OrderStatus::Completed, $merchantUser);

        $this->expectException(ValidationException::class);
        $service->cancelByAdmin($order->fresh(), $admin);
    }

    public function test_invalid_quantity_is_rejected_without_reducing_stock(): void
    {
        $product = $this->makeProduct(['stock' => 3]);
        $consumer = $this->makeConsumer();
        $service = app(OrderService::class);

        foreach ([0, -1, 21] as $quantity) {
            try {
                $service->place($consumer, $product, $quantity);
                $this->fail("Kuantitas {$quantity} seharusnya ditolak.");
            } catch (\Illuminate\Validation\ValidationException) {
                $this->assertSame(
                    3,
                    $product->fresh()->stock,
                    "Stok berubah untuk kuantitas {$quantity}."
                );
            }
        }
    }
}

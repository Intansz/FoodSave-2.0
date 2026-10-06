<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Exceptions\OrderException;
use App\Services\OrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\BuildsCatalog;
use Tests\TestCase;

class OrderServiceTest extends TestCase
{
    use BuildsCatalog, RefreshDatabase;

    public function test_order_reduces_stock_and_snapshots_price(): void
    {
        $product = $this->makeProduct(['stock' => 3]);

        $order = app(OrderService::class)->place($this->makeConsumer(), $product, 2);

        $this->assertSame(1, $product->fresh()->stock);
        $this->assertSame(14000, $order->subtotal);
        $this->assertSame(OrderStatus::Pending, $order->status);
        $this->assertSame('Donat Uji', $order->items->first()->product_name_snapshot);
        $this->assertCount(1, $order->histories);
    }

    public function test_cannot_order_more_than_remaining_stock(): void
    {
        $product = $this->makeProduct(['stock' => 3]);
        $service = app(OrderService::class);

        $service->place($this->makeConsumer(), $product, 2);

        $this->expectException(OrderException::class);
        try {
            $service->place($this->makeConsumer(), $product, 2); // sisa 1
        } finally {
            $this->assertSame(1, $product->fresh()->stock); // tidak pernah negatif
        }
    }

    public function test_old_order_keeps_price_after_product_is_edited(): void
    {
        $product = $this->makeProduct();
        $order = app(OrderService::class)->place($this->makeConsumer(), $product, 1);

        $product->update(['foodsave_price' => 9500, 'name' => 'Nama Baru']);

        $item = $order->fresh()->items->first();
        $this->assertSame(7000, $item->unit_price);
        $this->assertSame('Donat Uji', $item->product_name_snapshot);
    }

    public function test_service_fee_uses_configured_percentage_and_is_stored_as_snapshot(): void
    {
        config(['foodsave.service_fee_percent' => 10]);
        $product = $this->makeProduct(['foodsave_price' => 10000]);

        $order = app(OrderService::class)->place($this->makeConsumer(), $product, 2);

        $this->assertSame(2000, $order->service_fee);
        $this->assertSame(20000, $order->total); // konsumen tidak menanggung fee
    }

    public function test_expired_or_unapproved_products_cannot_be_ordered(): void
    {
        $expired = $this->makeProduct(['pickup_end' => now()->subMinute()]);
        $pending = $this->makeProduct([], approved: false);

        foreach ([$expired, $pending] as $product) {
            try {
                app(OrderService::class)->place($this->makeConsumer(), $product, 1);
                $this->fail('Order seharusnya ditolak.');
            } catch (OrderException) {
                $this->assertSame(3, $product->fresh()->stock);
            }
        }
    }
}

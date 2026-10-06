<?php

namespace Tests\Feature;

use App\Services\OrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\BuildsCatalog;
use Tests\TestCase;

class OrderControllerTest extends TestCase
{
    use BuildsCatalog, RefreshDatabase;

    public function test_consumer_cannot_view_another_consumers_order(): void
    {
        $product = $this->makeProduct();

        $consumerA = $this->makeConsumer();
        $consumerB = $this->makeConsumer();

        $order = app(OrderService::class)->place(
            $consumerA,
            $product,
            1
        );

        $this->actingAs($consumerB)
            ->get(route('orders.show', $order->id))
            ->assertNotFound();
    }
}
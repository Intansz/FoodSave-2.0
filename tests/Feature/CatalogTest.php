<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\BuildsCatalog;
use Tests\TestCase;

class CatalogTest extends TestCase
{
    use BuildsCatalog, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite(); // tidak butuh manifest build saat test
    }

    public function test_home_and_explore_render(): void
    {
        $this->get('/')->assertOk();
        $this->get('/jelajahi')->assertOk()->assertSee('Makanan tidak ditemukan');
    }

    public function test_explore_lists_only_available_products(): void
    {
        $this->makeProduct(['name' => 'Tersedia Enak']);
        $this->makeProduct(['name' => 'Stok Habis Roti', 'stock' => 0]);
        $this->makeProduct(['name' => 'Kadaluarsa Kue', 'pickup_end' => now()->subMinute()]);
        $this->makeProduct(['name' => 'Nonaktif Bolu', 'status' => Product::INACTIVE]);
        $this->makeProduct(['name' => 'Mitra Pending Pie'], approved: false);

        $this->get('/jelajahi')
            ->assertSee('Tersedia Enak')
            ->assertDontSee('Stok Habis Roti')
            ->assertDontSee('Kadaluarsa Kue')
            ->assertDontSee('Nonaktif Bolu')
            ->assertDontSee('Mitra Pending Pie');
    }

    public function test_search_and_price_filter(): void
    {
        $this->makeProduct(['name' => 'Donat Murah', 'foodsave_price' => 5000]);
        $this->makeProduct(['name' => 'Roti Mahal', 'foodsave_price' => 25000, 'normal_price' => 40000]);

        $this->get('/jelajahi?q=Donat')->assertSee('Donat Murah')->assertDontSee('Roti Mahal');
        $this->get('/jelajahi?harga=gt20')->assertSee('Roti Mahal')->assertDontSee('Donat Murah');
    }

    public function test_discount_is_calculated_automatically(): void
    {
        $product = $this->makeProduct(['normal_price' => 20000, 'foodsave_price' => 5000]);

        $this->assertSame(75, $product->discount_percentage);
    }

    public function test_guest_is_sent_to_login_when_ordering_and_merchant_is_forbidden(): void
    {
        $product = $this->makeProduct();

        $this->get("/produk/{$product->id}/pesan")->assertRedirect('/masuk');
        $this->actingAs($product->merchant->user)->get("/produk/{$product->id}/pesan")->assertForbidden();
        $this->actingAs($this->makeConsumer())->get("/produk/{$product->id}/pesan?qty=2")->assertOk()->assertSee('Buat Pesanan');
    }
}

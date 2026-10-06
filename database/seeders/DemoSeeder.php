<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Enums\VerificationStatus;
use App\Models\Category;
use App\Models\Merchant;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * DATA CONTOH untuk pengembangan lokal saja (dipanggil hanya di environment "local").
 * Semua nama mitra & produk di sini fiktif. Login: email di bawah, password "password".
 */
class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@foodsave.test'],
            [
                'name' => 'Admin FoodSave',
                'password' => 'password',
            ]
        );

        if (! $admin->isAdmin()) {
            $admin->forceFill(['role' => UserRole::Admin])->save();
        }

        $admin->refresh();

        User::create(['name' => 'Alya (Demo)', 'email' => 'alya@foodsave.test', 'password' => 'password']);

        $merchants = [
            ['Bakery Demo Mendalo', 'Mendalo', 'rina@foodsave.test'],
            ['Warung Demo Telanaipura', 'Telanaipura', 'warung@foodsave.test'],
            ['Kafe Demo Kota Baru', 'Kota Baru', 'kafe@foodsave.test'],
        ];

        $cat = fn(string $slug) => Category::where('slug', $slug)->value('id');

        $catalog = [
            0 => [
                ['Croissant Butter Almond', 'bakery-pastry', 25000, 9000, 4, 1, 3],
                ['Donat Coklat Keju', 'bakery-pastry', 18000, 7000, 9, 2, 5],
                ['Roti Sobek Susu', 'bakery-pastry', 22000, 11000, 6, 3, 6],
            ],
            1 => [
                ['Nasi Ayam Teriyaki', 'makanan-berat', 28000, 12000, 2, 1, 2],
                ['Paket Nasi Rendang', 'makanan-berat', 30000, 15000, 5, 2, 6],
                ['Salad Sayur Segar', 'healthy-food', 24000, 13000, 3, 2, 5],
            ],
            2 => [
                ['Matcha Oat Latte', 'minuman', 24000, 10000, 5, 1, 4],
                ['Es Kopi Susu Gula Aren', 'minuman', 20000, 9000, 7, 2, 6],
                ['Brownies Coklat', 'dessert', 20000, 8000, 8, 3, 7],
                ['Keripik Tempe Pedas', 'snack', 15000, 6000, 12, 4, 8],
            ],
        ];

        foreach ($merchants as $i => [$name, $area, $email]) {
            $owner = User::create(['name' => "Pemilik {$name}", 'email' => $email, 'password' => 'password']);
            $owner->forceFill(['role' => UserRole::Merchant])->save();

            $merchant = new Merchant([
                'business_name' => $name,
                'description' => 'Mitra demo untuk pengembangan.',
                'phone' => '081200000000',
                'address' => "Jl. Contoh No. {$i}, {$area}, Jambi",
                'area' => $area,
                'maps_url' => 'https://maps.google.com/?q=Jambi',
                'operating_hours' => '08.00–21.00',
            ]);
            $merchant->user_id = $owner->id;
            $merchant->verification_status = VerificationStatus::Approved;
            $merchant->is_active = true;
            $merchant->save();

            foreach ($catalog[$i] as [$product, $slug, $normal, $price, $stock, $startH, $endH]) {
                $item = new Product([
                    'category_id' => $cat($slug),
                    'name' => $product,
                    'description' => 'Makanan surplus layak konsumsi, dijual dengan harga lebih hemat.',
                    'normal_price' => $normal,
                    'foodsave_price' => $price,
                    'stock' => $stock,
                    'pickup_start' => now()->addMinutes($startH * 10 - 20),
                    'pickup_end' => now()->addHours($endH),
                    'status' => Product::ACTIVE,
                ]);
                $item->merchant_id = $merchant->id;
                $item->save();
            }
        }
    }
}

<?php

namespace Tests\Support;

use App\Enums\UserRole;
use App\Enums\VerificationStatus;
use App\Models\Category;
use App\Models\Merchant;
use App\Models\Product;
use App\Models\User;

trait BuildsCatalog
{
    protected function makeProduct(array $overrides = [], bool $approved = true): Product
    {
        $owner = User::create(['name' => 'Mitra Uji', 'email' => uniqid('m').'@test.test', 'password' => 'password']);
        $owner->forceFill(['role' => UserRole::Merchant])->save();

        $merchant = new Merchant([
            'business_name' => 'Mitra Uji', 'phone' => '0812000000',
            'address' => 'Jl. Uji No. 1', 'area' => 'Mendalo',
        ]);
        $merchant->user_id = $owner->id;
        $merchant->verification_status = $approved ? VerificationStatus::Approved : VerificationStatus::Pending;
        $merchant->is_active = true;
        $merchant->save();

        $category = Category::firstOrCreate(['slug' => 'bakery-pastry'], ['name' => 'Bakery & Pastry']);

        $product = new Product(array_merge([
            'category_id' => $category->id,
            'name' => 'Donat Uji',
            'normal_price' => 18000,
            'foodsave_price' => 7000,
            'stock' => 3,
            'pickup_start' => now()->subHour(),
            'pickup_end' => now()->addHours(2),
            'status' => Product::ACTIVE,
        ], $overrides));
        $product->merchant_id = $merchant->id;
        $product->save();

        return $product;
    }

    protected function makeConsumer(): User
    {
        $user = User::create([
            'name' => 'Alya Uji',
            'email' => uniqid('c').'@test.test',
            'password' => 'password',
        ]);

        $user->forceFill([
            'role' => UserRole::Consumer,
        ])->save();

        return $user;
    }
}

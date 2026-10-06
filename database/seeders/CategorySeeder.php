<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Seed awal dari prototype. "Flash Deal" SENGAJA tidak menjadi kategori:
     * ia turunan data (lihat Product::scopeFlashDeal). Finalisasi daftar
     * kategori = PRD Open Question #11.
     */
    public function run(): void
    {
        foreach (['Makanan Berat', 'Bakery & Pastry', 'Dessert', 'Minuman', 'Snack', 'Healthy Food'] as $name) {
            Category::updateOrCreate(['slug' => Str::slug($name)], ['name' => $name]);
        }
    }
}

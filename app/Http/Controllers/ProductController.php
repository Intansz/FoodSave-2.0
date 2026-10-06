<?php

namespace App\Http\Controllers;

use App\Enums\VerificationStatus;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /** Rentang harga (Rupiah) → [min, max]. Kunci dipakai di query string. */
    public const PRICE_RANGES = [
        'lt10' => ['label' => 'Di bawah Rp10.000', 'min' => null, 'max' => 9999],
        '10-20' => ['label' => 'Rp10.000 – Rp20.000', 'min' => 10000, 'max' => 20000],
        'gt20' => ['label' => 'Di atas Rp20.000', 'min' => 20001, 'max' => null],
    ];

    public const SORTS = [
        'terbaru' => 'Terbaru',
        'termurah' => 'Harga Terendah',
        'diskon' => 'Diskon Terbesar',
        'pickup' => 'Pickup Terdekat',
    ];

    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));
        $category = (string) $request->query('category', '');
        $area = (string) $request->query('area', '');
        $price = (string) $request->query('harga', '');
        $sort = (string) $request->query('sort', 'terbaru');
        $flash = $request->boolean('flash');

        $range = self::PRICE_RANGES[$price] ?? null;
        $sort = array_key_exists($sort, self::SORTS) ? $sort : 'terbaru';

        $products = Product::query()
            ->available()
            ->with('merchant')
            ->when($q !== '', fn ($query) => $query->search(mb_substr($q, 0, 100)))
            ->when($category !== '', fn ($query) => $query->whereHas('category', fn ($c) => $c->where('slug', $category)))
            ->when(in_array($area, config('foodsave.areas'), true), fn ($query) => $query->whereHas('merchant', fn ($m) => $m->where('area', $area)))
            ->when($range, fn ($query) => $query->priceRange($range['min'], $range['max']))
            ->when($flash, fn ($query) => $query->flashDeal())
            ->orderedBy($sort)
            ->paginate(12)
            ->withQueryString();

        return view('products.index', [
            'products' => $products,
            'categories' => Category::where('is_active', true)->orderBy('id')->get(),
            'filters' => [
                'q' => $q,
                'category' => $category,
                'area' => $area,
                'harga' => $range ? $price : '',
                'sort' => $sort,
                'flash' => $flash,
            ],
        ]);
    }

    public function show(Product $product): View
    {
        $product->load(['merchant', 'category']);

        // Produk dari mitra yang belum disetujui tidak boleh terlihat publik (PRD FR-01).
        abort_unless($product->merchant->verification_status === VerificationStatus::Approved, 404);

        return view('products.show', ['product' => $product]);
    }
}

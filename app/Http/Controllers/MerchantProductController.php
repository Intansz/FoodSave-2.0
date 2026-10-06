<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class MerchantProductController extends Controller
{
    public function index(Request $request): View
    {
        $merchant = $request->user()->merchant;

        abort_unless($merchant, 404);

        $products = $merchant->products()
            ->with('category')
            ->latest()
            ->paginate(10);

        return view('merchant.products.index', [
            'merchant' => $merchant,
            'products' => $products,
        ]);
    }

    public function create(Request $request): View
    {
        $merchant = $request->user()->merchant;

        abort_unless($merchant, 404);

        $categories = Category::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('merchant.products.create', [
            'merchant' => $merchant,
            'categories' => $categories,
        ]);
    }

    public function store(Request $request)
    {
        $merchant = $request->user()->merchant;

        abort_unless($merchant, 404);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'normal_price' => ['required', 'integer', 'min:0'],
            'foodsave_price' => ['required', 'integer', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'pickup_start' => ['required', 'date'],
            'pickup_end' => ['required', 'date', 'after:pickup_start'],
            'status' => ['required', 'in:active,inactive'],
        ]);
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        $merchant->products()->create($validated);

        return redirect()
            ->route('merchant.products.index')
            ->with('status', 'Produk berhasil ditambahkan.');
    }
    public function edit(Request $request, int $product): View
    {
        $merchant = $request->user()->merchant;

        abort_unless($merchant, 404);

        $product = $merchant->products()
            ->with('category')
            ->findOrFail($product);

        $categories = Category::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('merchant.products.edit', [
            'merchant' => $merchant,
            'product' => $product,
            'categories' => $categories,
        ]);
    }
    public function update(Request $request, int $product)
    {
        $merchant = $request->user()->merchant;

        abort_unless($merchant, 404);

        $product = $merchant->products()->findOrFail($product);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'description' => ['nullable', 'string'],
            'normal_price' => ['required', 'integer', 'min:0'],
            'foodsave_price' => ['required', 'integer', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'pickup_start' => ['required', 'date'],
            'pickup_end' => ['required', 'date', 'after:pickup_start'],
            'status' => ['required', 'in:active,inactive'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($validated);

        return redirect()
            ->route('merchant.products.index')
            ->with('status', 'Produk berhasil diperbarui.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
            'category_id' => [
                'required',
                \Illuminate\Validation\Rule::exists('categories', 'id')
                    ->where('is_active', true),
            ],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'normal_price' => ['required', 'integer', 'min:1'],
            'foodsave_price' => [
                'required',
                'integer',
                'min:0',
                'lt:normal_price',
            ],
            'stock' => ['required', 'integer', 'min:0'],
            'pickup_start' => ['required', 'date'],
            'pickup_end' => ['required', 'date', 'after:pickup_start'],
            'status' => ['required', 'in:active,inactive'],
        ]);
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');

            if ($imagePath === false) {
                return back()
                    ->withInput()
                    ->withErrors(['image' => 'Gagal mengunggah gambar. Silakan coba lagi.']);
            }

            $validated['image'] = $imagePath;
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
            'category_id' => [
                'required',
                \Illuminate\Validation\Rule::exists('categories', 'id')
                    ->where('is_active', true),
            ],
            'description' => ['nullable', 'string'],
            'normal_price' => ['required', 'integer', 'min:1'],
            'foodsave_price' => [
                'required',
                'integer',
                'min:0',
                'lt:normal_price',
            ],
            'stock' => ['required', 'integer', 'min:0'],
            'pickup_start' => ['required', 'date'],
            'pickup_end' => ['required', 'date', 'after:pickup_start'],
            'status' => ['required', 'in:active,inactive'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        if ($request->hasFile('image')) {
            $newImagePath = $request->file('image')->store('products', 'public');

            if ($newImagePath === false) {
                return back()
                    ->withInput()
                    ->withErrors(['image' => 'Gagal mengunggah gambar. Silakan coba lagi.']);
            }

            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }

            $validated['image'] = $newImagePath;
        }

        $product->update($validated);

        return redirect()
            ->route('merchant.products.index')
            ->with('status', 'Produk berhasil diperbarui.');
    }
}

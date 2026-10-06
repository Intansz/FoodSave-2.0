<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Exceptions\OrderException;
use App\Http\Requests\StoreOrderRequest;
use App\Models\Order;
use App\Models\Product;
use App\Services\OrderService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $tab = $request->query('tab', 'semua');

        $orders = $request->user()->orders()
            ->with(['merchant', 'items'])
            ->when($tab === 'berlangsung', fn ($q) => $q->whereNotIn('status', [OrderStatus::Completed->value, OrderStatus::Cancelled->value]))
            ->when($tab === 'selesai', fn ($q) => $q->where('status', OrderStatus::Completed->value))
            ->when($tab === 'dibatalkan', fn ($q) => $q->where('status', OrderStatus::Cancelled->value))
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('orders.index', ['orders' => $orders, 'tab' => $tab]);
    }

    /** Halaman Order Confirmation (belum membuat order). */
    public function create(Request $request, Product $product): View|RedirectResponse
    {
        $product->load('merchant');

        if (! $product->isAvailable()) {
            return redirect()->route('products.show', $product)
                ->withErrors(['order' => $product->unavailableReason() ?? 'Produk tidak tersedia.']);
        }

        $quantity = max(1, min((int) $request->query('qty', 1), $product->stock, 20));

        return view('orders.create', ['product' => $product, 'quantity' => $quantity]);
    }

    public function store(StoreOrderRequest $request, OrderService $orders): RedirectResponse
    {
        $data = $request->validated();
        $product = Product::findOrFail($data['product_id']);

        try {
            $order = $orders->place($request->user(), $product, (int) $data['quantity']);
        } catch (OrderException $e) {
            return redirect()->route('products.show', $product)->withErrors(['order' => $e->getMessage()]);
        }

        return redirect()->route('orders.show', $order)
            ->with('status', 'Pesanan berhasil dibuat. Bayar langsung ke mitra saat mengambil makanan.');
    }

    public function show(Request $request, Order $order): View
    {
        abort_unless($order->user_id === $request->user()->id, 404);

        $order->load(['merchant', 'items', 'histories']);

        return view('orders.show', ['order' => $order]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Models\ServiceFee;
use App\Notifications\OrderStatusUpdated;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class MerchantController extends Controller
{
    public function dashboard(Request $request): View
    {
        $merchant = $request->user()->merchant;

        abort_unless($merchant, 404);

        $merchant->load([
            'products' => fn($query) => $query->latest(),
        ]);

        $orders = $merchant->orders()
            ->with(['user', 'items.product'])
            ->latest('ordered_at')
            ->get();

        $summary = [
            'total_products' => $merchant->products->count(),
            'active_products' => $merchant->products
                ->where('status', 'active')
                ->count(),
            'pending_orders' => $orders
                ->where('status', OrderStatus::Pending)
                ->count(),
            'ongoing_orders' => $orders
                ->filter(fn($order) => $order->status?->isOngoing())
                ->count(),
        ];

        return view('merchant.dashboard', [
            'merchant' => $merchant,
            'products' => $merchant->products,
            'orders' => $orders,
            'summary' => $summary,
        ]);
    }
    public function updateOrderStatus(Request $request, int $order): RedirectResponse
    {
        $merchant = $request->user()->merchant;

        abort_unless($merchant, 404);

        $order = $merchant->orders()->findOrFail($order);

        $validated = $request->validate([
            'status' => ['required', 'in:confirmed,ready_for_pickup,completed,cancelled'],
        ]);

        $order->update([
            'status' => $validated['status'],
        ]);
        $order->user->notify(new OrderStatusUpdated($order));

        if ($validated['status'] === OrderStatus::Completed->value) {
            ServiceFee::firstOrCreate(
                ['order_id' => $order->id],
                [
                    'merchant_id' => $order->merchant_id,
                    'fee_percentage' => $order->service_fee_percent,
                    'transaction_amount' => $order->subtotal,
                    'fee_amount' => $order->service_fee,
                    'status' => 'unsettled',
                ]
            );
        }

        return redirect()
            ->route('merchant.dashboard')
            ->with('status', 'Status pesanan berhasil diperbarui.');
    }
}

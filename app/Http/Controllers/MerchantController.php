<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Models\ServiceFee;
use App\Notifications\OrderStatusUpdated;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Services\OrderStatusService;
use Illuminate\Validation\ValidationException;

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
    public function updateOrderStatus(
        Request $request,
        int $order,
        OrderStatusService $statusService
    ): RedirectResponse {
        $merchant = $request->user()->merchant;

        abort_unless($merchant, 404);

        $orderModel = $merchant->orders()->findOrFail($order);

        $validated = $request->validate([
            'status' => [
                'required',
                'in:confirmed,ready_for_pickup,completed,cancelled',
            ],
        ]);

        try {
            $updatedOrder = $statusService->updateByMerchant(
                $orderModel,
                OrderStatus::from($validated['status']),
                $request->user()
            );
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors());
        }

        $updatedOrder->user->notify(
            new OrderStatusUpdated($updatedOrder)
        );

        return redirect()
            ->route('merchant.dashboard')
            ->with('status', 'Status pesanan berhasil diperbarui.');
    }
}

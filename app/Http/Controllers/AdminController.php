<?php

namespace App\Http\Controllers;

use App\Models\Merchant;
use App\Models\Order;
use App\Models\Product;
use App\Models\ServiceFee;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;


class AdminController extends Controller
{
    public function dashboard(Request $request): View
    {
        $summary = [
            'total_merchants' => Merchant::count(),
            'pending_merchants' => Merchant::where('verification_status', 'pending')->count(),
            'total_products' => Product::count(),
            'total_orders' => Order::count(),
            'unsettled_fees' => ServiceFee::where('status', 'unsettled')->sum('fee_amount'),
        ];

        return view('admin.dashboard', [
            'summary' => $summary,
        ]);
    }
    public function merchants(Request $request): View
    {
        $merchants = Merchant::query()
            ->with('user')
            ->latest()
            ->paginate(10);

        return view('admin.merchants', [
            'merchants' => $merchants,
        ]);
    }
    public function updateMerchantStatus(Request $request, int $merchant): RedirectResponse
    {
        $merchant = Merchant::findOrFail($merchant);

        $validated = $request->validate([
            'verification_status' => [
                'required',
                'in:approved,rejected,suspended',
            ],
        ]);

        $merchant->update([
            'verification_status' => $validated['verification_status'],
        ]);

        return redirect()
            ->route('admin.merchants')
            ->with('status', 'Status mitra berhasil diperbarui.');
    }
}

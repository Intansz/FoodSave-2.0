<?php

namespace App\Http\Controllers;

use App\Models\ServiceFee;
use App\Models\Settlement;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminSettlementController extends Controller
{
    public function index(Request $request): View
    {
        $fees = ServiceFee::query()
            ->with(['merchant', 'order'])
            ->where('status', 'unsettled')
            ->latest()
            ->get();

        return view('admin.settlements', [
            'fees' => $fees,
        ]);
    }

    public function settle(Request $request, int $merchant): RedirectResponse
    {
        $fees = ServiceFee::query()
            ->where('merchant_id', $merchant)
            ->where('status', 'unsettled')
            ->get();

        if ($fees->isEmpty()) {
            return redirect()
                ->route('admin.settlements')
                ->with('status', 'Tidak ada service fee yang perlu disettle.');
        }

        DB::transaction(function () use ($fees, $merchant) {
            $settlement = Settlement::create([
                'merchant_id' => $merchant,
                'total_fee' => $fees->sum('fee_amount'),
                'status' => 'completed',
                'settled_at' => now(),
            ]);

            ServiceFee::whereIn('id', $fees->pluck('id'))
                ->update([
                    'settlement_id' => $settlement->id,
                    'status' => 'settled',
                ]);
        });

        return redirect()
            ->route('admin.settlements')
            ->with('status', 'Service fee berhasil disettle.');
    }
}

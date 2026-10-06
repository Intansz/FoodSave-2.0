<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $flashDeals = Product::query()->available()->flashDeal()
            ->with('merchant')->orderBy('pickup_end')->limit(4)->get();

        $latest = Product::query()->available()
            ->with('merchant')->latest('id')->limit(8)->get();

        return view('home', [
            'categories' => Category::where('is_active', true)->orderBy('id')->get(),
            'flashDeals' => $flashDeals,
            'latest' => $latest,
            'featured' => $flashDeals->first() ?? $latest->first(),
        ]);
    }
}

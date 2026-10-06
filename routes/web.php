<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MerchantController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MerchantProductController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminSettlementController;
use App\Http\Controllers\NotificationController;

/* Publik ------------------------------------------------------------------ */

Route::get('/', HomeController::class)->name('home');
Route::get('/jelajahi', [ProductController::class, 'index'])->name('products.index');
Route::get('/produk/{product}', [ProductController::class, 'show'])->name('products.show');

/* Tamu -------------------------------------------------------------------- */
Route::middleware('guest')->group(function () {
    Route::get('/masuk', [LoginController::class, 'create'])->name('login');
    Route::post('/masuk', [LoginController::class, 'store'])->middleware('throttle:6,1');

    Route::get('/daftar', [RegisterController::class, 'createConsumer'])->name('register');
    Route::post('/daftar', [RegisterController::class, 'storeConsumer']);

    Route::get('/jadi-mitra', [RegisterController::class, 'createMerchant'])->name('register.merchant');
    Route::post('/jadi-mitra', [RegisterController::class, 'storeMerchant']);
});

Route::post('/keluar', [LoginController::class, 'destroy'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/notifikasi', [NotificationController::class, 'index'])
        ->name('notifications.index');
});

/* Konsumen ---------------------------------------------------------------- */
Route::middleware(['auth', 'role:consumer'])->group(function () {
    Route::get('/produk/{product}/pesan', [OrderController::class, 'create'])->name('orders.create');
    Route::post('/pesanan', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/pesanan', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/pesanan/{order}', [OrderController::class, 'show'])->name('orders.show');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');

    Route::patch('/pesanan/{order}/cancel', [AdminController::class, 'cancelOrder'])
    ->name('orders.cancel');

    Route::get('/mitra', [AdminController::class, 'merchants'])->name('merchants');

    Route::patch('/mitra/{merchant}/status', [AdminController::class, 'updateMerchantStatus'])
    ->name('merchants.status');

    Route::get('/settlements', [AdminSettlementController::class, 'index'])
    ->name('settlements');

Route::post('/settlements/{merchant}/settle', [AdminSettlementController::class, 'settle'])
    ->name('settlements.settle');
});
Route::middleware(['auth', 'role:merchant'])->prefix('mitra')->name('merchant.')->group(function () {
    Route::get('/', [MerchantController::class, 'dashboard'])->name('dashboard');

    Route::patch('/pesanan/{order}/status', [MerchantController::class, 'updateOrderStatus'])
        ->name('orders.status');

    Route::get('/produk', [MerchantProductController::class, 'index'])->name('products.index');

    Route::get('/produk/tambah', [MerchantProductController::class, 'create'])->name('products.create');

    Route::post('/produk', [MerchantProductController::class, 'store'])->name('products.store');

    Route::get('/produk/{product}/edit', [MerchantProductController::class, 'edit'])->name('products.edit');

    Route::put('/produk/{product}', [MerchantProductController::class, 'update'])->name('products.update');
});
// Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(...);

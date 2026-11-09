<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (!auth()->check()) {
        return redirect()->route('login');
    }

    return redirect()->route(
        in_array(auth()->user()->role, ['admin', 'seller']) ? 'dashboard' : 'products.index'
    );
});

// Dipakai JavaScript untuk mengecek akun aktif (sinkron antar tab)
Route::get('/whoami', function () {
    return response()
        ->json(['id' => auth()->id() ?? 0])
        ->header('Cache-Control', 'no-store, no-cache, must-revalidate');
})->name('whoami');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);

    Route::get('/register', [RegisterController::class, 'choose'])->name('register');
    Route::get('/register/{role}', [RegisterController::class, 'showForm'])
        ->whereIn('role', ['customer', 'seller', 'admin'])
        ->name('register.form');
    Route::post('/register/{role}', [RegisterController::class, 'store'])
        ->whereIn('role', ['customer', 'seller', 'admin'])
        ->name('register.store');
});

Route::middleware(['auth', 'active'])->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Customer: belanja | Admin & seller: kelola produk
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');

    // Admin & seller
    Route::middleware('role:admin,seller')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::resource('products', ProductController::class)->except(['index', 'show']);
    });

    // Admin saja: detail penjualan tiap seller
    Route::middleware('role:admin')->group(function () {
        Route::get('/dashboard/sellers/{seller}', [DashboardController::class, 'showSeller'])
            ->name('dashboard.seller');
    });

    // Customer & admin: keranjang dan beli
    Route::middleware('role:customer,admin')->group(function () {
        Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
        Route::post('/cart/checkout', [CartController::class, 'checkout'])->name('cart.checkout');
        Route::post('/cart/add/{product}', [CartController::class, 'add'])->name('cart.add');
        Route::patch('/cart/{item}', [CartController::class, 'update'])->name('cart.update');
        Route::delete('/cart/{item}', [CartController::class, 'remove'])->name('cart.remove');
    });
});
<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\UserAddressController;
use App\Http\Controllers\CartPageController;
use App\Http\Controllers\CheckoutPageController;
use App\Http\Controllers\OrdersPageController;
use App\Http\Controllers\AddressesPageController;
use App\Models\UserAddress;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Shop pages (Blade)
Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');
Route::get('/shop/{product}', [ShopController::class, 'show'])->name('shop.show');

// Authenticated routes
Route::middleware('auth')->group(function () {
    // Add to cart from product page
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');

    // Cart page and item actions
    Route::get('/cart', [CartPageController::class, 'index'])->name('cart.index');
    Route::patch('/cart/items/{item}', [CartController::class, 'updateItem'])->name('cart.items.update');
    Route::delete('/cart/items/{item}', [CartController::class, 'removeItem'])->name('cart.items.destroy');

    // Checkout page and submit
    Route::get('/checkout', [CheckoutPageController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [OrderController::class, 'checkout'])->name('checkout.store');

    // Orders pages
    Route::get('/orders', [OrdersPageController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrdersPageController::class, 'show'])->name('orders.show');

    // Addresses page and basic CRUD
    Route::get('/addresses', [AddressesPageController::class, 'index'])->name('addresses.index');
    Route::post('/addresses', [UserAddressController::class, 'store'])->name('addresses.store');
    Route::delete('/addresses/{address}', [UserAddressController::class, 'destroy'])->name('addresses.destroy');
    Route::post('/addresses/{address}/default', [UserAddressController::class, 'setDefault'])->name('addresses.default');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

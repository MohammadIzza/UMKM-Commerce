<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\CartController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Shop pages (Blade)
Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');
Route::get('/shop/{product}', [ShopController::class, 'show'])->name('shop.show');

// Minimal cart route for add-to-cart from product page (requires login)
Route::middleware('auth')->group(function () {
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
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

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\UserAddressController;
use App\Http\Controllers\ShippingMethodController;

Route::get('/', function () {
    return view('welcome');
});

// Public catalog endpoints (JSON for now)
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{product}', [ProductController::class, 'show']); // product by slug
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{category}', [CategoryController::class, 'show']); // category by slug
Route::get('/shipping-methods', [ShippingMethodController::class, 'index']);

// Product reviews (list per product)
Route::get('/products/{product}/reviews', [ReviewController::class, 'index']);

// Authenticated actions
Route::middleware('web')->group(function () {
    // Note: In real app, also gate with auth middleware; here kept simple
    Route::get('/cart', [CartController::class, 'show']);
    Route::post('/cart/add', [CartController::class, 'add']);
    Route::patch('/cart/items/{item}', [CartController::class, 'updateItem']);
    Route::delete('/cart/items/{item}', [CartController::class, 'removeItem']);

    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/{order}', [OrderController::class, 'show']);
    Route::post('/checkout', [OrderController::class, 'checkout']);

    Route::post('/products/{product}/reviews', [ReviewController::class, 'store']);

    Route::get('/addresses', [UserAddressController::class, 'index']);
    Route::post('/addresses', [UserAddressController::class, 'store']);
    Route::put('/addresses/{address}', [UserAddressController::class, 'update']);
    Route::delete('/addresses/{address}', [UserAddressController::class, 'destroy']);
});

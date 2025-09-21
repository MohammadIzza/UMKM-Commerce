<?php

namespace App\Http\Controllers;

use App\Models\ShippingMethod;
use App\Models\UserAddress;
use App\Models\Cart;
use Illuminate\Support\Facades\Auth;

class CheckoutPageController extends Controller
{
    public function index()
    {
        $cart = Cart::with('items.product')->firstOrCreate(['user_id' => Auth::id()]);
        $addresses = UserAddress::where('user_id', Auth::id())
            ->orderByDesc('is_default')
            ->orderByDesc('id')
            ->get();
        $shippingMethods = ShippingMethod::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
        return view('checkout.index', compact('cart', 'addresses', 'shippingMethods'));
    }
}

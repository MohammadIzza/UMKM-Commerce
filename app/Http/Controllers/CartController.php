<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        $cart = Cart::firstOrCreate(['user_id' => $user->id]);
        $cart->load(['items.product']);
        return response()->json($cart);
    }

    public function add(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'qty' => 'nullable|integer|min:1',
        ]);
        $user = Auth::user();
        $cart = Cart::firstOrCreate(['user_id' => $user->id]);
        $product = Product::findOrFail($data['product_id']);
        $qty = $data['qty'] ?? 1;

        $item = CartItem::firstOrNew([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
        ]);
        $item->qty = ($item->exists ? $item->qty : 0) + $qty;
        $item->price = $product->price;
        $item->save();

        return response()->json(['message' => 'Added to cart', 'item' => $item->load('product')]);
    }

    public function updateItem(Request $request, CartItem $item)
    {
        $data = $request->validate([
            'qty' => 'required|integer|min:1',
        ]);
        $item->update(['qty' => $data['qty']]);
        return response()->json(['message' => 'Cart item updated', 'item' => $item->load('product')]);
    }

    public function removeItem(CartItem $item)
    {
        $item->delete();
        return response()->json(['message' => 'Cart item removed']);
    }
}

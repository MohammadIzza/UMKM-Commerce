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
        $newQty = ($item->exists ? $item->qty : 0) + $qty;
        
        // Check stock availability
        if ($newQty > $product->stock) {
            $message = $item->exists 
                ? "Produk sudah ada di keranjang dengan jumlah {$item->qty}. Stok tersedia: {$product->stock}"
                : "Stok tidak mencukupi. Stok tersedia: {$product->stock}";
                
            if ($request->wantsJson()) {
                return response()->json([
                    'message' => $message,
                    'stock' => $product->stock,
                    'current_qty' => $item->exists ? $item->qty : 0
                ], 422);
            }
            return redirect()->back()->with('error', $message);
        }
        
        $item->qty = $newQty;
        $item->price = $product->price;
        $item->save();

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Added to cart', 'item' => $item->load('product')]);
        }
        return redirect()->back()->with('success', 'Produk ditambahkan ke keranjang');
    }

    public function updateItem(Request $request, CartItem $item)
    {
        // Ensure item belongs to current user's cart
        if ($item->cart->user_id !== Auth::id()) {
            abort(403);
        }
        $data = $request->validate([
            'qty' => 'required|integer|min:1',
        ]);
        
        // Check stock availability
        if ($data['qty'] > $item->product->stock) {
            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Stok tidak mencukupi. Stok tersedia: ' . $item->product->stock,
                    'stock' => $item->product->stock
                ], 422);
            }
            return redirect()->route('cart.index')->with('error', 'Stok tidak mencukupi. Stok tersedia: ' . $item->product->stock);
        }
        
        $item->update(['qty' => $data['qty']]);
        if ($request->wantsJson()) {
            return response()->json(['message' => 'Cart item updated', 'item' => $item->load('product')]);
        }
        return redirect()->route('cart.index')->with('success', 'Jumlah produk diperbarui');
    }

    public function removeItem(Request $request, CartItem $item)
    {
        if ($item->cart->user_id !== Auth::id()) {
            abort(403);
        }
        $item->delete();
        if ($request->wantsJson()) {
            return response()->json(['message' => 'Cart item removed']);
        }
        return redirect()->route('cart.index')->with('success', 'Produk dihapus dari keranjang');
    }
}

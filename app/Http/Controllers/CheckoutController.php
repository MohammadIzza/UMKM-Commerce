<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\ShippingMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckoutController extends Controller
{
    public function index()
    {
        // Get user's active cart with items
        $cart = Cart::with(['items.product'])
            ->where('user_id', Auth::id())
            ->where('status', 'active')
            ->firstOrFail();

        // Get available shipping methods
        $shipping_methods = ShippingMethod::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return view('checkout', compact('cart', 'shipping_methods'));
    }

    public function process(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'shipping_address' => 'required|string',
            'shipping_city' => 'required|string|max:255',
            'shipping_province' => 'required|string|max:255',
            'shipping_postal_code' => 'required|string|max:10',
            'shipping_method_id' => 'required|exists:shipping_methods,id',
            'payment_method' => 'required|in:bank_transfer',
        ]);

        // Get cart
        $cart = Cart::with(['items.product'])
            ->where('user_id', Auth::id())
            ->where('status', 'active')
            ->firstOrFail();

        // Get shipping method
        $shipping_method = ShippingMethod::findOrFail($request->shipping_method_id);

        // Create order
        $order = Order::create([
            'user_id' => Auth::id(),
            'order_number' => 'ORD-' . strtoupper(uniqid()),
            'customer_name' => $validated['customer_name'],
            'customer_email' => Auth::user()->email,
            'customer_phone' => $validated['customer_phone'],
            'shipping_address' => $validated['shipping_address'],
            'shipping_city' => $validated['shipping_city'],
            'shipping_province' => $validated['shipping_province'],
            'shipping_postal_code' => $validated['shipping_postal_code'],
            'shipping_method_id' => $shipping_method->id,
            'shipping_cost' => $shipping_method->base_cost,
            'subtotal' => $cart->subtotal,
            'total' => $cart->subtotal + $shipping_method->base_cost,
            'status' => 'pending',
            'payment_method' => $validated['payment_method'],
            'payment_status' => 'pending',
        ]);

        // Create order items
        foreach ($cart->items as $item) {
            $order->items()->create([
                'product_id' => $item->product_id,
                'product_name' => $item->product->name,
                'price' => $item->product->price,
                'qty' => $item->qty,
                'subtotal' => $item->subtotal,
            ]);
        }

        // Mark cart as completed
        $cart->update(['status' => 'completed']);

        return redirect()->route('orders.show', $order)
            ->with('success', 'Order placed successfully! Please complete the payment.');
    }
}
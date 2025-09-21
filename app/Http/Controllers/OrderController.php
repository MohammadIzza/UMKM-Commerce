<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ShippingMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::where('user_id', Auth::id())
            ->with('items')
            ->orderByDesc('id')
            ->paginate(10);
        return response()->json($orders);
    }

    public function show(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }
        $order->load(['items.product']);
        return response()->json($order);
    }

    public function checkout(Request $request)
    {
        $data = $request->validate([
            'shipping_method_code' => 'required|string',
            'customer_name' => 'required|string',
            'customer_email' => 'required|email',
            'customer_phone' => 'required|string',
            'shipping_address' => 'required|string',
            'shipping_city' => 'required|string',
            'shipping_province' => 'required|string',
            'shipping_postal_code' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $user = Auth::user();
        $cart = Cart::with('items.product')->where('user_id', $user->id)->first();
        if (!$cart || $cart->items->isEmpty()) {
            return response()->json(['message' => 'Cart is empty'], 422);
        }

        $method = ShippingMethod::where('code', $data['shipping_method_code'])->first();
        if (!$method) {
            return response()->json(['message' => 'Invalid shipping method'], 422);
        }

        return DB::transaction(function () use ($user, $cart, $method, $data) {
            $subtotal = $cart->items->sum(fn ($i) => $i->qty * $i->price);
            $totalWeight = $cart->items->sum(fn ($i) => ($i->product->weight ?? 0) * $i->qty);
            $shippingCost = (float)$method->base_cost + (float)$method->cost_per_kg * ceil($totalWeight);
            $tax = 0; $discount = 0; $total = $subtotal + $shippingCost + $tax - $discount;

            $order = Order::create([
                'user_id' => $user->id,
                'order_number' => 'ORD-' . Str::upper(Str::random(8)),
                'subtotal' => $subtotal,
                'shipping_cost' => $shippingCost,
                'tax_amount' => $tax,
                'discount_amount' => $discount,
                'total' => $total,
                'status' => 'pending',
                'customer_name' => $data['customer_name'],
                'customer_email' => $data['customer_email'],
                'customer_phone' => $data['customer_phone'],
                'shipping_address' => $data['shipping_address'],
                'shipping_city' => $data['shipping_city'],
                'shipping_province' => $data['shipping_province'],
                'shipping_postal_code' => $data['shipping_postal_code'] ?? null,
                'shipping_method' => $method->name,
                'tracking_number' => null,
                'payment_method' => null,
                'payment_status' => 'pending',
                'payment_proof' => null,
                'payment_verified_at' => null,
                'notes' => $data['notes'] ?? null,
                'admin_notes' => null,
            ]);

            foreach ($cart->items as $ci) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $ci->product_id,
                    'product_name' => $ci->product->name,
                    'product_sku' => $ci->product->sku,
                    'product_image' => $ci->product->image,
                    'price' => $ci->price,
                    'qty' => $ci->qty,
                    'subtotal' => $ci->qty * $ci->price,
                ]);
            }

            // Clear cart
            $cart->items()->delete();

            return response()->json(['message' => 'Order created', 'order' => $order->load('items')]);
        });
    }
}

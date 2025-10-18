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
        // Support both JSON API and Blade form post.
        // Blade form posts: address_id or address fields, and shipping_method_id
        $isForm = !$request->wantsJson();

        if ($isForm) {
            $data = $request->validate([
                'address_id' => 'nullable|exists:user_addresses,id',
                'recipient_name' => 'nullable|string',
                'phone' => 'nullable|string',
                'address_line1' => 'nullable|string',
                'city' => 'nullable|string',
                'province' => 'nullable|string',
                'postal_code' => 'nullable|string',
                'shipping_method_id' => 'required|exists:shipping_methods,id',
                'notes' => 'nullable|string',
            ]);
        } else {
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
        }

        $user = Auth::user();
        $cart = Cart::with('items.product')->where('user_id', $user->id)->first();
        if (!$cart || $cart->items->isEmpty()) {
            return response()->json(['message' => 'Cart is empty'], 422);
        }

        $method = $isForm
            ? ShippingMethod::find($data['shipping_method_id'])
            : ShippingMethod::where('code', $data['shipping_method_code'])->first();
        if (!$method) {
            return response()->json(['message' => 'Invalid shipping method'], 422);
        }

        return DB::transaction(function () use ($user, $cart, $method, $data, $isForm) {
            // Ensure cart has items
            if ($cart->items->isEmpty()) {
                throw new \Exception('Cart is empty. Cannot create order.');
            }
            
            $subtotal = $cart->items->sum(fn ($i) => $i->qty * $i->price);
            $totalWeight = $cart->items->sum(fn ($i) => ($i->product->weight ?? 0) * $i->qty);
            $shippingCost = (float)$method->base_cost + (float)($method->cost_per_kg ?? 0) * ceil($totalWeight);
            $tax = 0; $discount = 0; $total = $subtotal + $shippingCost + $tax - $discount;
            
            if ($isForm) {
                // Resolve address fields
                if (!empty($data['address_id'])) {
                    $addr = \App\Models\UserAddress::where('user_id', $user->id)->find($data['address_id']);
                    if (!$addr) {
                        return response()->json(['message' => 'Invalid address'], 422);
                    }
                    $customer_name = $addr->recipient_name;
                    $customer_phone = $addr->phone;
                    $shipping_address = $addr->address_line_1;
                    $shipping_city = $addr->city;
                    $shipping_province = $addr->province;
                    $shipping_postal_code = $addr->postal_code;
                } else {
                    $customer_name = $data['recipient_name'] ?? $user->name;
                    $customer_phone = $data['phone'] ?? ($user->phone ?? '');
                    $shipping_address = $data['address_line1'] ?? '';
                    $shipping_city = $data['city'] ?? '';
                    $shipping_province = $data['province'] ?? '';
                    $shipping_postal_code = $data['postal_code'] ?? null;
                }
                $customer_email = $user->email;
            } else {
                $customer_name = $data['customer_name'];
                $customer_email = $data['customer_email'];
                $customer_phone = $data['customer_phone'];
                $shipping_address = $data['shipping_address'];
                $shipping_city = $data['shipping_city'];
                $shipping_province = $data['shipping_province'];
                $shipping_postal_code = $data['shipping_postal_code'] ?? null;
            }

            $order = Order::create([
                'user_id' => $user->id,
                'order_number' => 'ORD-' . Str::upper(Str::random(8)),
                'subtotal' => $subtotal,
                'shipping_cost' => $shippingCost,
                'tax_amount' => $tax,
                'discount_amount' => $discount,
                'total' => $total,
                'status' => 'pending',
                'customer_name' => $customer_name,
                'customer_email' => $customer_email,
                'customer_phone' => $customer_phone,
                'shipping_address' => $shipping_address,
                'shipping_city' => $shipping_city,
                'shipping_province' => $shipping_province,
                'shipping_postal_code' => $shipping_postal_code,
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
                // Check stock availability before creating order item
                if ($ci->product->stock < $ci->qty) {
                    throw new \Exception("Stok tidak mencukupi untuk produk: {$ci->product->name}. Stok tersedia: {$ci->product->stock}");
                }
                
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
                
                // Reduce product stock
                $ci->product->decrement('stock', $ci->qty);
                
                // Update sold count for analytics
                $ci->product->increment('sold_count', $ci->qty);
            }

            // Clear cart
            $cart->items()->delete();

            if ($isForm) {
                return redirect()->route('orders.show', $order)->with('success', 'Pesanan berhasil dibuat');
            }
            return response()->json(['message' => 'Order created', 'order' => $order->load('items')]);
        });
    }
}

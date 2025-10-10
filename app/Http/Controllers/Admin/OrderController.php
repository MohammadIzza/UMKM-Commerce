<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with(['user', 'items.product', 'shippingMethod'])
            ->latest()
            ->paginate(10);

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load(['user', 'items.product', 'shippingMethod']);
        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,processing,shipped,delivered,cancelled,refunded'
        ]);

        // Update status dan timestamp terkait
        $now = now();
        if ($validated['status'] == 'confirmed') {
            $order->confirmed_at = $now;
        } elseif ($validated['status'] == 'shipped') {
            $order->shipped_at = $now;
        }
        
        $order->status = $validated['status'];
        $order->save();

        return redirect()
            ->route('admin.orders.show', $order)
            ->with('success', 'Order status updated successfully');
    }
}
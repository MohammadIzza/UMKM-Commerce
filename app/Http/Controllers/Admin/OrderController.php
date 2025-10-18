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
        // Check if order can be modified
        if (!$order->canBeModified()) {
            return redirect()
                ->route('admin.orders.show', $order)
                ->with('error', 'Cannot modify order with status: ' . ucfirst($order->status) . '. Order is already finalized.');
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,processing,shipped,delivered,cancelled,refunded'
        ]);

        // Check if the status transition is allowed
        $availableTransitions = $order->getAvailableStatusTransitions();
        if (!empty($availableTransitions) && !in_array($validated['status'], $availableTransitions)) {
            return redirect()
                ->route('admin.orders.show', $order)
                ->with('error', 'Invalid status transition. Available transitions from ' . ucfirst($order->status) . ': ' . implode(', ', array_map('ucfirst', $availableTransitions)));
        }

        // Update status dan timestamp terkait
        $now = now();
        if ($validated['status'] == 'confirmed') {
            $order->confirmed_at = $now;
        } elseif ($validated['status'] == 'shipped') {
            $order->shipped_at = $now;
        } elseif ($validated['status'] == 'delivered') {
            $order->delivered_at = $now;
        } elseif ($validated['status'] == 'cancelled') {
            $order->cancelled_at = $now;
        }
        
        $order->status = $validated['status'];
        $order->save();

        return redirect()
            ->route('admin.orders.show', $order)
            ->with('success', 'Order status updated successfully to ' . ucfirst($validated['status']));
    }
}
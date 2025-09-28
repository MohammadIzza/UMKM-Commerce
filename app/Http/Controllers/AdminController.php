<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_orders' => Order::count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'total_products' => Product::count(),
            'total_customers' => User::where('role', 'customer')->count(),
        ];

        $recent_orders = Order::with('user')
            ->latest()
            ->take(5)
            ->get();

        $low_stock_products = Product::where('stock', '<', 10)
            ->with('images')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recent_orders', 'low_stock_products'));
    }
}
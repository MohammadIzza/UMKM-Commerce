<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function dashboard()
    {
        // Get statistics for the dashboard
        $stats = [
            'total_orders' => Order::count(),
            'pending_orders' => Order::where('status', 'processing')->count(),
            'total_products' => Product::count(),
            'total_customers' => User::where('role', 'customer')->count(),
        ];

        // Get recent orders
        $recent_orders = Order::with(['user'])
            ->latest()
            ->take(5)
            ->get();

        // Get low stock products (less than 10 items)
        $low_stock_products = Product::where('stock', '<', 10)
            ->take(5)
            ->get();

        // Get monthly revenue for the current year
        $monthly_revenue = Order::where('status', 'completed')
            ->whereYear('created_at', now()->year)
            ->selectRaw('SUM(total) as revenue, MONTH(created_at) as month')
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('revenue', 'month')
            ->toArray();

        return view('admin.dashboard', compact(
            'stats',
            'recent_orders',
            'low_stock_products',
            'monthly_revenue'
        ));
    }
}
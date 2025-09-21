<?php

namespace App\Http\Controllers;

use App\Models\ShippingMethod;

class ShippingMethodController extends Controller
{
    public function index()
    {
        $methods = ShippingMethod::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
        return response()->json($methods);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query()
            ->with(['category', 'images'])
            ->where('is_active', true);

        if ($search = $request->string('q')->toString()) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('description', 'like', "%$search%");
            });
        }

        if ($category = $request->string('category')->toString()) {
            $query->whereHas('category', function ($q) use ($category) {
                $q->where('slug', $category);
            });
        }

        $products = $query->orderByDesc('id')->paginate(12);
        return response()->json($products);
    }

    public function show(Product $product)
    {
        $product->load(['category', 'images', 'reviews.user']);
        return response()->json($product);
    }
}

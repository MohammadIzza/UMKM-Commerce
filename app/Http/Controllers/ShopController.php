<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();

        $query = Product::query()
            ->where('is_active', true)
            ->with('images')
            ->withCount('reviews')
            ->withAvg('reviews', 'rating');
            
        if ($search = $request->string('q')->toString()) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('description', 'like', "%$search%");
            });
        }
        if ($cat = $request->string('category')->toString()) {
            $query->whereHas('category', fn ($q) => $q->where('slug', $cat));
        }

        $products = $query->latest()->paginate(12)->withQueryString();

        return view('shop.index', compact('categories', 'products'));
    }

    public function show(Product $product)
    {
        $product->load(['category', 'images', 'reviews.user']);
        return view('shop.show', compact('product'));
    }
}

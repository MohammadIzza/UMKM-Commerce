<?php

namespace App\Http\Controllers;

use App\Models\Category;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        return response()->json($categories);
    }

    public function show(Category $category)
    {
        $category->load(['products' => function ($q) {
            $q->where('is_active', true)->with('images');
        }]);
        return response()->json($category);
    }
}

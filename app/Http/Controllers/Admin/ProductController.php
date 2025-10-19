<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with(['category', 'images'])
            ->latest()
            ->paginate(10);

        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'description' => 'required',
            'price' => 'required|integer|min:1',
            'stock' => 'required|integer|min:1',
            'category_id' => 'required|exists:categories,id',
            'images.*' => 'image|mimes:jpeg,png,jpg|max:2048'
        ], [
            'price.integer' => 'Harga harus berupa angka bulat Rupiah',
            'price.min' => 'Harga minimal Rp 1',
            'stock.integer' => 'Stok harus berupa angka bulat',
            'stock.min' => 'Stok minimal 1 unit'
        ]);

        // Generate slug from name
        $slug = Str::slug($validated['name']);
        $baseSlug = $slug;
        $counter = 1;

        // Ensure unique slug
        while (Product::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        $validated['slug'] = $slug;
        $product = Product::create($validated);

        if ($request->hasFile('images')) {
            $sortOrder = 0;
            $firstImagePath = null;
            foreach ($request->file('images') as $image) {
                $path = $image->store('products', 'public');
                
                // Save first image path for product.image field
                if ($sortOrder === 0) {
                    $firstImagePath = $path;
                }
                
                $product->images()->create([
                    'image_path' => $path,
                    'alt_text' => $product->name,
                    'is_primary' => $sortOrder === 0,
                    'sort_order' => $sortOrder++
                ]);
            }
            
            // Update product with first image
            if ($firstImagePath) {
                $product->update(['image' => $firstImagePath]);
            }
        }

        return redirect()
            ->route('admin.products')
            ->with('success', 'Product created successfully');
    }

    public function edit(Product $product)
    {
        $categories = Category::all();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'description' => 'required',
            'price' => 'required|integer|min:1',
            'stock' => 'required|integer|min:1',
            'category_id' => 'required|exists:categories,id',
            'images.*' => 'image|mimes:jpeg,png,jpg|max:2048'
        ], [
            'price.integer' => 'Harga harus berupa angka bulat Rupiah',
            'price.min' => 'Harga minimal Rp 1',
            'stock.integer' => 'Stok harus berupa angka bulat',
            'stock.min' => 'Stok minimal 1 unit'
        ]);

        $product->update($validated);

        if ($request->hasFile('images')) {
            $sortOrder = $product->images()->max('sort_order') ?? -1;
            $isFirstImage = $product->images()->count() === 0;
            $firstNewImagePath = null;
            
            foreach ($request->file('images') as $image) {
                $path = $image->store('products', 'public');
                $sortOrder++;
                
                // Save first new image if product has no images
                if ($isFirstImage && !$firstNewImagePath) {
                    $firstNewImagePath = $path;
                }
                
                $product->images()->create([
                    'image_path' => $path,
                    'alt_text' => $product->name,
                    'is_primary' => $isFirstImage,
                    'sort_order' => $sortOrder
                ]);
                
                $isFirstImage = false;
            }
            
            // Update product.image if this is the first image uploaded
            if ($firstNewImagePath && !$product->image) {
                $product->update(['image' => $firstNewImagePath]);
            }
        }

        return redirect()
            ->route('admin.products')
            ->with('success', 'Product updated successfully');
    }

    public function destroy(Product $product)
    {
        // Delete associated images from storage
        foreach ($product->images as $image) {
            Storage::disk('public')->delete($image->image_path);
        }

        $product->delete();

        return redirect()
            ->route('admin.products')
            ->with('success', 'Product deleted successfully');
    }

    public function deleteImage(ProductImage $image)
    {
        Storage::disk('public')->delete($image->image_path);
        $image->delete();

        return response()->json(['success' => true]);
    }
}
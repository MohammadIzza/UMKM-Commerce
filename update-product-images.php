<?php

// Script untuk update field image pada products yang kosong
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$products = App\Models\Product::whereNull('image')
    ->orWhere('image', '')
    ->get();

echo "Found {$products->count()} products without image field\n";

foreach ($products as $product) {
    $firstImage = $product->images()->orderBy('sort_order')->first();
    
    if ($firstImage) {
        $product->update(['image' => $firstImage->image_path]);
        echo "✓ Updated: {$product->name} -> {$firstImage->image_path}\n";
    } else {
        echo "✗ No images for: {$product->name}\n";
    }
}

echo "\nDone!\n";

<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $samples = [
            ['name' => 'Keripik Pisang Manis 200g', 'price' => 15000, 'weight' => 0.2, 'category' => 'Makanan & Minuman'],
            ['name' => 'Sambal Roa 150g', 'price' => 28000, 'weight' => 0.15, 'category' => 'Makanan & Minuman'],
            ['name' => 'Batik Tulis Pria Lengan Panjang', 'price' => 250000, 'weight' => 0.4, 'category' => 'Fashion'],
            ['name' => 'Gelang Manik Manik Handmade', 'price' => 45000, 'weight' => 0.05, 'category' => 'Kerajinan'],
            ['name' => 'Minyak Telon 60ml', 'price' => 23000, 'weight' => 0.1, 'category' => 'Kesehatan & Kecantikan'],
            ['name' => 'Sabun Herbal Rempah', 'price' => 15000, 'weight' => 0.1, 'category' => 'Kesehatan & Kecantikan'],
            ['name' => 'Sapu Ijuk Tradisional', 'price' => 18000, 'weight' => 0.6, 'category' => 'Rumah Tangga'],
            ['name' => 'Keranjang Rotan Anyam', 'price' => 65000, 'weight' => 0.8, 'category' => 'Kerajinan'],
            ['name' => 'Charger USB 2A', 'price' => 35000, 'weight' => 0.12, 'category' => 'Elektronik'],
            ['name' => 'Kabel Data Micro USB 1m', 'price' => 15000, 'weight' => 0.05, 'category' => 'Elektronik'],
        ];

        foreach ($samples as $i => $s) {
            $category = Category::where('name', $s['category'])->first();
            if (!$category) continue;

            $slug = Str::slug($s['name']);
            Product::updateOrCreate(
                ['slug' => $slug],
                [
                    'sku' => 'SKU'.str_pad((string)($i+1), 4, '0', STR_PAD_LEFT),
                    'name' => $s['name'],
                    'description' => 'Produk UMKM lokal berkualitas.',
                    'short_description' => 'Produk UMKM Indonesia.',
                    'price' => $s['price'],
                    'sale_price' => null,
                    'stock' => 50,
                    'min_stock' => 5,
                    'weight' => $s['weight'],
                    'dimensions' => null,
                    'category_id' => $category->id,
                    'image' => null,
                    'gallery' => [],
                    'is_active' => true,
                    'is_featured' => false,
                    'view_count' => 0,
                    'sold_count' => 0,
                    'rating_average' => 0,
                    'rating_count' => 0,
                    'meta_title' => $s['name'],
                    'meta_description' => 'Belanja '.$s['name'].' dari UMKM lokal.',
                ]
            );
        }
    }
}

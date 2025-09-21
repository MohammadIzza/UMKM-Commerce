<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductReview;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProductReviewSeeder extends Seeder
{
    public function run(): void
    {
        $buyer = User::where('email', 'buyer@umkm.test')->first();
        if (!$buyer) return;

        $products = Product::inRandomOrder()->take(3)->get();
        foreach ($products as $p) {
            ProductReview::updateOrCreate(
                ['user_id' => $buyer->id, 'product_id' => $p->id],
                [
                    'rating' => rand(4, 5),
                    'title' => 'Mantap!',
                    'comment' => 'Produk UMKM berkualitas, recomended.',
                    'images' => [],
                    'is_verified_purchase' => false,
                    'is_approved' => true,
                ]
            );
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class CartSeeder extends Seeder
{
    public function run(): void
    {
        $buyer = User::where('email', 'buyer@umkm.test')->first();
        if (!$buyer) return;

        $cart = Cart::firstOrCreate(['user_id' => $buyer->id]);

        // add up to 2 items
        $products = Product::inRandomOrder()->take(2)->get();
        foreach ($products as $p) {
            CartItem::updateOrCreate(
                ['cart_id' => $cart->id, 'product_id' => $p->id],
                [
                    'qty' => 1,
                    'price' => $p->price,
                ]
            );
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Makanan & Minuman', 'description' => 'Produk kuliner UMKM lokal'],
            ['name' => 'Fashion', 'description' => 'Pakaian, batik, dan aksesoris'],
            ['name' => 'Kesehatan & Kecantikan', 'description' => 'Perawatan tubuh dan herbal'],
            ['name' => 'Rumah Tangga', 'description' => 'Peralatan rumah dan kebutuhan harian'],
            ['name' => 'Kerajinan', 'description' => 'Kerajinan tangan khas daerah'],
            ['name' => 'Elektronik', 'description' => 'Aksesoris gadget dan perangkat'],
        ];

        foreach ($categories as $cat) {
            $slug = Str::slug($cat['name']);
            Category::updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => $cat['name'],
                    'description' => $cat['description'],
                    'image' => null,
                    'is_active' => true,
                ]
            );
        }
    }
}

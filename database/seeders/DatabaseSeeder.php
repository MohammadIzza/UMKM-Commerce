<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed base taxonomy and data
        $this->call([
            UsersSeeder::class,
            CategorySeeder::class,
            ProductSeeder::class,
            ShippingMethodSeeder::class,
            UserAddressSeeder::class,
            CartSeeder::class,
            ProductReviewSeeder::class,
        ]);

        // Optional: sample factory user
        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}

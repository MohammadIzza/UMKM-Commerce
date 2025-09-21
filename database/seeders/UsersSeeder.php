<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::updateOrCreate(
            ['email' => 'admin@umkm.test'],
            [
                'name' => 'Admin UMKM',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'is_active' => true,
            ]
        );

        // Buyer
        User::updateOrCreate(
            ['email' => 'buyer@umkm.test'],
            [
                'name' => 'Pembeli UMKM',
                'password' => Hash::make('password'),
                'role' => 'user',
                'is_active' => true,
            ]
        );
    }
}

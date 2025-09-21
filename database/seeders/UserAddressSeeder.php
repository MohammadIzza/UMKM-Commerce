<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserAddress;
use Illuminate\Database\Seeder;

class UserAddressSeeder extends Seeder
{
    public function run(): void
    {
        $buyer = User::where('email', 'buyer@umkm.test')->first();
        if (!$buyer) {
            return;
        }

        UserAddress::updateOrCreate(
            ['user_id' => $buyer->id, 'label' => 'Rumah'],
            [
                'recipient_name' => 'Pembeli UMKM',
                'phone' => '081234567890',
                'address_line_1' => 'Jl. Mawar No. 10',
                'address_line_2' => 'RT 01 RW 02',
                'city' => 'Kota Bandung',
                'province' => 'Jawa Barat',
                'postal_code' => '40123',
                'latitude' => -6.914744,
                'longitude' => 107.609810,
                'is_default' => true,
            ]
        );
    }
}

<?php

namespace Database\Seeders;

use App\Models\ShippingMethod;
use Illuminate\Database\Seeder;

class ShippingMethodSeeder extends Seeder
{
    public function run(): void
    {
        $methods = [
            ['name' => 'JNE Reguler', 'code' => 'JNE_REG', 'base_cost' => 10000, 'cost_per_kg' => 8000, 'estimated_days' => '2-4 hari'],
            ['name' => 'SiCepat Reg', 'code' => 'SICEPAT_REG', 'base_cost' => 9000, 'cost_per_kg' => 7000, 'estimated_days' => '1-3 hari'],
            ['name' => 'POS Kilat', 'code' => 'POS_KILAT', 'base_cost' => 8000, 'cost_per_kg' => 6500, 'estimated_days' => '3-5 hari'],
        ];

        foreach ($methods as $m) {
            ShippingMethod::updateOrCreate(
                ['code' => $m['code']],
                [
                    'name' => $m['name'],
                    'description' => null,
                    'base_cost' => $m['base_cost'],
                    'cost_per_kg' => $m['cost_per_kg'],
                    'estimated_days' => $m['estimated_days'],
                    'max_weight' => null,
                    'available_areas' => null,
                    'is_active' => true,
                    'sort_order' => 0,
                ]
            );
        }
    }
}

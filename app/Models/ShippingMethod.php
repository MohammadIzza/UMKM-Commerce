<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingMethod extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'code', 'description', 'base_cost', 'cost_per_kg',
        'estimated_days', 'max_weight', 'available_areas', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'base_cost' => 'decimal:2',
        'cost_per_kg' => 'decimal:2',
        'available_areas' => 'array',
        'is_active' => 'boolean',
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'order_number', 'subtotal', 'shipping_cost', 'tax_amount',
        'discount_amount', 'total', 'status',
        'customer_name', 'customer_email', 'customer_phone',
        'shipping_address', 'shipping_city', 'shipping_province', 'shipping_postal_code',
        'shipping_method', 'tracking_number',
        'payment_method', 'payment_status', 'payment_proof', 'payment_verified_at',
        'confirmed_at', 'shipped_at', 'delivered_at', 'cancelled_at',
        'notes', 'admin_notes',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total' => 'decimal:2',
        'payment_verified_at' => 'datetime',
        'confirmed_at' => 'datetime',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function shippingMethod(): BelongsTo
    {
        return $this->belongsTo(ShippingMethod::class);
    }

    protected static function boot()
    {
        parent::boot();
        
        static::saving(function ($order) {
            // Calculate subtotal from items
            $subtotal = $order->items->sum('subtotal');
            $order->subtotal = $subtotal;
            
            // Calculate total
            $order->total = $subtotal + $order->shipping_cost - $order->discount_amount + $order->tax_amount;
        });
    }
}

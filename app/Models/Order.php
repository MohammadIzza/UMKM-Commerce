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

    /**
     * Check if order can be modified (status can be changed)
     */
    public function canBeModified(): bool
    {
        return !in_array($this->status, ['confirmed', 'cancelled', 'refunded']);
    }

    /**
     * Check if order is in final status (cannot be changed)
     */
    public function isFinalStatus(): bool
    {
        return in_array($this->status, ['confirmed', 'cancelled', 'refunded']);
    }

    /**
     * Get available status transitions for current order status
     */
    public function getAvailableStatusTransitions(): array
    {
        if ($this->isFinalStatus()) {
            return []; // No transitions allowed for final statuses
        }

        switch ($this->status) {
            case 'pending':
                return ['confirmed', 'cancelled'];
            case 'processing':
                return ['shipped', 'cancelled'];
            case 'shipped':
                return ['delivered', 'cancelled'];
            case 'delivered':
                return ['refunded']; // Can only refund delivered orders
            default:
                return [];
        }
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

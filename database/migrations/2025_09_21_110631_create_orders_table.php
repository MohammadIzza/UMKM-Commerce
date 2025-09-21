<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('order_number')->unique();
            $table->decimal('subtotal', 12, 2);
            $table->decimal('shipping_cost', 10, 2)->default(0);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('total', 12, 2);
            $table->enum('status', [
                'pending', 'confirmed', 'processing', 'shipped', 
                'delivered', 'cancelled', 'refunded'
            ])->default('pending');
            
            // Customer Info
            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('customer_phone', 20);
            
            // Shipping Info
            $table->text('shipping_address');
            $table->string('shipping_city');
            $table->string('shipping_province');
            $table->string('shipping_postal_code', 10)->nullable();
            $table->string('shipping_method')->nullable();
            $table->string('tracking_number')->nullable();
            
            // Payment Info
            $table->enum('payment_method', [
                'bank_transfer', 'cod', 'ewallet', 'credit_card'
            ])->nullable();
            $table->enum('payment_status', [
                'pending', 'paid', 'failed', 'expired', 'refunded'
            ])->default('pending');
            $table->string('payment_proof')->nullable();
            $table->timestamp('payment_verified_at')->nullable();
            
            // Timestamps
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            
            $table->text('notes')->nullable();
            $table->text('admin_notes')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'status']);
            $table->index('status');
            $table->index('payment_status');
            $table->index('created_at');
            $table->index('order_number');
        });
    }

    public function down()
    {
        Schema::dropIfExists('orders');
    }
};
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::table('orders', function (Blueprint $table) {
            // Check if columns don't already exist before adding them
            if (!Schema::hasColumn('orders', 'shipping_method')) {
                $table->string('shipping_method')->nullable()->after('customer_phone');
            }
            if (!Schema::hasColumn('orders', 'shipping_cost')) {
                $table->decimal('shipping_cost', 10, 2)->default(0)->after('shipping_method');
            }
            if (!Schema::hasColumn('orders', 'payment_method')) {
                $table->enum('payment_method', ['transfer', 'cod', 'ewallet'])->nullable()->after('shipping_cost');
            }
            if (!Schema::hasColumn('orders', 'payment_status')) {
                $table->enum('payment_status', ['pending', 'paid', 'failed'])->default('pending')->after('payment_method');
            }
            if (!Schema::hasColumn('orders', 'payment_proof')) {
                $table->string('payment_proof')->nullable()->after('payment_status');
            }
            if (!Schema::hasColumn('orders', 'shipped_at')) {
                $table->timestamp('shipped_at')->nullable()->after('payment_proof');
            }
            if (!Schema::hasColumn('orders', 'delivered_at')) {
                $table->timestamp('delivered_at')->nullable()->after('shipped_at');
            }
            if (!Schema::hasColumn('orders', 'tracking_number')) {
                $table->string('tracking_number')->nullable()->after('delivered_at');
            }
        });
    }

    public function down() {
        Schema::table('orders', function (Blueprint $table) {
            // Only drop columns if they exist and were added by this migration
            $columnsToCheck = [
                'shipping_method', 'shipping_cost', 'payment_method', 
                'payment_status', 'payment_proof', 'shipped_at', 
                'delivered_at', 'tracking_number'
            ];
            
            foreach ($columnsToCheck as $column) {
                if (Schema::hasColumn('orders', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
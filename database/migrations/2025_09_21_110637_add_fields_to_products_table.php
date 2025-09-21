<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'weight')) {
                $table->decimal('weight', 8, 2)->nullable()->after('stock'); // kg
            }
            if (!Schema::hasColumn('products', 'sku')) {
                $table->string('sku', 50)->unique()->nullable()->after('id');
            }
            if (!Schema::hasColumn('products', 'view_count')) {
                $table->integer('view_count')->default(0)->after('is_active');
            }
        });
    }

    public function down() {
        Schema::table('products', function (Blueprint $table) {
            // Only drop columns if they exist and were added by this migration
            if (Schema::hasColumn('products', 'weight')) {
                $table->dropColumn('weight');
            }
            if (Schema::hasColumn('products', 'sku')) {
                $table->dropColumn('sku');
            }
            if (Schema::hasColumn('products', 'view_count')) {
                $table->dropColumn('view_count');
            }
        });
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('shipping_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique(); // JNE, TIKI, POS
            $table->text('description')->nullable();
            $table->decimal('base_cost', 10, 2);
            $table->decimal('cost_per_kg', 8, 2)->nullable();
            $table->string('estimated_days', 20)->nullable(); // "2-3 hari"
            $table->integer('max_weight')->nullable(); // kg
            $table->json('available_areas')->nullable(); // supported areas
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            
            $table->index('is_active');
        });
    }

    public function down()
    {
        Schema::dropIfExists('shipping_methods');
    }
};
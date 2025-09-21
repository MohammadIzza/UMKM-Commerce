<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cart_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->integer('qty')->default(1);
            $table->decimal('price', 10, 2); // price when added to cart
            $table->timestamps();
            
            $table->unique(['cart_id', 'product_id']);
            $table->index('cart_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('cart_items');
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            // Tambahkan kolom shipping_method_id setelah kolom shipping_postal_code
            $table->foreignId('shipping_method_id')->nullable()->after('shipping_postal_code')->constrained();
            
            // Ubah kolom shipping_method lama menjadi nullable karena akan kita migrasi ke shipping_method_id
            $table->string('shipping_method')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['shipping_method_id']);
            $table->dropColumn('shipping_method_id');
            $table->string('shipping_method')->nullable(false)->change();
        });
    }
};
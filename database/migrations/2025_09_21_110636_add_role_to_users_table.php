<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'user'])->default('user')->after('password');
            $table->string('phone', 20)->nullable()->after('email');
            $table->date('date_of_birth')->nullable()->after('phone');
            $table->enum('gender', ['male', 'female'])->nullable()->after('date_of_birth');
            $table->string('avatar')->nullable()->after('gender');
            $table->timestamp('last_login_at')->nullable()->after('avatar');
            $table->boolean('is_active')->default(true)->after('last_login_at');
            
            $table->index('role');
            $table->index('is_active');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'role', 'phone', 'date_of_birth', 'gender', 
                'avatar', 'last_login_at', 'is_active'
            ]);
        });
    }
};
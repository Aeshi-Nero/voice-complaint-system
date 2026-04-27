<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_first_login')->default(true)->after('role');
            $table->string('temporary_pin', 4)->nullable()->after('is_first_login');
            $table->rememberToken()->after('password');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['is_first_login', 'temporary_pin', 'remember_token']);
        });
    }
};

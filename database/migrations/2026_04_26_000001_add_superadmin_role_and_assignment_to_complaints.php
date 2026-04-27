<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Update users role enum
        if (config('database.default') === 'pgsql') {
            DB::statement("ALTER TYPE user_role_enum ADD VALUE 'superadmin'");
        } else {
            Schema::table('users', function (Blueprint $table) {
                $table->enum('role', ['student', 'staff', 'admin', 'superadmin'])->default('student')->change();
            });
        }

        // Add assigned_to and rating to complaints
        Schema::table('complaints', function (Blueprint $table) {
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->integer('rating')->nullable();
        });
    }

    public function down()
    {
        Schema::table('complaints', function (Blueprint $table) {
            $table->dropForeign(['assigned_to']);
            $table->dropColumn(['assigned_to', 'rating']);
        });

        // Note: Removing enum value in down() is complex in pgsql, usually left as is
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('complaint_messages', function (Blueprint $table) {
            $table->json('images')->nullable()->after('message');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('banned_until')->nullable()->after('is_blocked');
        });
    }

    public function down(): void
    {
        Schema::table('complaint_messages', function (Blueprint $table) {
            $table->dropColumn('images');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('banned_until');
        });
    }
};

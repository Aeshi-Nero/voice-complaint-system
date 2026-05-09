<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('profanity_words', function (Blueprint $table) {
            $table->string('language')->default('en')->after('word');
            $table->string('type')->default('profanity')->after('language'); // profanity, slang, dialect
            $table->index(['language', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('profanity_words', function (Blueprint $table) {
            $table->dropIndex(['language', 'type']);
            $table->dropColumn(['language', 'type']);
        });
    }
};

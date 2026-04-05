<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('id_number')->unique();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->enum('role', ['student', 'staff', 'admin'])->default('student');
            $table->boolean('is_blocked')->default(false);
            $table->integer('complaints_today')->default(0);
            $table->timestamp('last_complaint_reset')->nullable();
            $table->string('course')->nullable();
            $table->timestamps();
            
            $table->index('id_number');
            $table->index('role');
            $table->index('is_blocked');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
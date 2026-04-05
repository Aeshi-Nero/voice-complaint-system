<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('complaints', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('complaint_number')->unique();
            $table->enum('category', ['Academic', 'Faculty', 'Administrative', 'IT/Technical', 'Health & Safety']);
            $table->enum('priority', ['Low', 'Medium', 'High']);
            $table->string('title');
            $table->text('description');
            $table->string('image_path')->nullable();
            $table->enum('status', ['pending', 'in_progress', 'resolved', 'rejected'])->default('pending');
            $table->text('admin_notes')->nullable();
            $table->timestamp('submitted_at');
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
            
            $table->index('status');
            $table->index('category');
            $table->index('user_id');
            $table->index('complaint_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('complaints');
    }
};
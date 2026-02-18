<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            
            $table->tinyInteger('rating')->unsigned(); // 1-5
            $table->string('title')->nullable();
            $table->text('comment')->nullable();
            
            // Review status
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            
            // Helpful votes
            $table->unsignedInteger('helpful_count')->default(0);
            $table->unsignedInteger('not_helpful_count')->default(0);
            
            // Admin response
            $table->text('admin_response')->nullable();
            $table->timestamp('admin_response_at')->nullable();
            
            // Verified purchase
            $table->boolean('is_verified_purchase')->default(false);
            
            $table->timestamps();
            
            $table->unique(['user_id', 'product_id']); // One review per product per user
            $table->index(['product_id', 'status']);
            $table->index('rating');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};

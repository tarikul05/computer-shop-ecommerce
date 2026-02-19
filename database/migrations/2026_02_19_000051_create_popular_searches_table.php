<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('popular_searches', function (Blueprint $table) {
            $table->id();
            $table->string('query')->unique();
            $table->unsignedInteger('search_count')->default(0);
            $table->unsignedInteger('click_count')->default(0);
            $table->unsignedInteger('conversion_count')->default(0);
            $table->timestamp('last_searched_at')->nullable();
            $table->timestamps();

            $table->index('search_count');
            $table->index('last_searched_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('popular_searches');
    }
};

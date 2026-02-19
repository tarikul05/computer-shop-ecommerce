<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add full-text search indexes to products table
     */
    public function up(): void
    {
        // Add fulltext index for MySQL
        Schema::table('products', function (Blueprint $table) {
            $table->fullText(['name', 'description', 'short_description', 'sku'], 'products_fulltext_index');
        });

        // Add fulltext index to categories
        Schema::table('categories', function (Blueprint $table) {
            $table->fullText(['name', 'description'], 'categories_fulltext_index');
        });

        // Add fulltext index to brands
        Schema::table('brands', function (Blueprint $table) {
            $table->fullText(['name', 'description'], 'brands_fulltext_index');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropFullText('products_fulltext_index');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropFullText('categories_fulltext_index');
        });

        Schema::table('brands', function (Blueprint $table) {
            $table->dropFullText('brands_fulltext_index');
        });
    }
};

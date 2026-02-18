<?php

use App\Modules\Product\Controllers\CategoryController;
use App\Modules\Product\Controllers\BrandController;
use App\Modules\Product\Controllers\ProductController;
use App\Modules\Product\Controllers\Admin\CategoryAdminController;
use App\Modules\Product\Controllers\Admin\BrandAdminController;
use App\Modules\Product\Controllers\Admin\ProductAdminController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Product Module Routes
|--------------------------------------------------------------------------
*/

// Public Routes
// Categories
Route::prefix('categories')->group(function () {
    Route::get('/', [CategoryController::class, 'index']);
    Route::get('/tree', [CategoryController::class, 'tree']);
    Route::get('/featured', [CategoryController::class, 'featured']);
    Route::get('/filter', [CategoryController::class, 'forFilter']);
    Route::get('/{slug}', [CategoryController::class, 'show']);
});

// Brands
Route::prefix('brands')->group(function () {
    Route::get('/', [BrandController::class, 'index']);
    Route::get('/featured', [BrandController::class, 'featured']);
    Route::get('/filter', [BrandController::class, 'forFilter']);
    Route::get('/{slug}', [BrandController::class, 'show']);
});

// Products
Route::prefix('products')->group(function () {
    Route::get('/', [ProductController::class, 'index']);
    Route::get('/featured', [ProductController::class, 'featured']);
    Route::get('/new-arrivals', [ProductController::class, 'newArrivals']);
    Route::get('/best-sellers', [ProductController::class, 'bestSellers']);
    Route::get('/on-sale', [ProductController::class, 'onSale']);
    Route::get('/search', [ProductController::class, 'search']);
    Route::get('/filter-options', [ProductController::class, 'filterOptions']);
    Route::get('/{slug}', [ProductController::class, 'show']);
    Route::get('/{slug}/related', [ProductController::class, 'related']);
});

/*
|--------------------------------------------------------------------------
| Admin Routes (Protected)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->middleware('auth:sanctum')->group(function () {
    // Categories Management
    Route::prefix('categories')->group(function () {
        Route::get('/', [CategoryAdminController::class, 'index']);
        Route::get('/tree', [CategoryAdminController::class, 'tree']);
        Route::post('/', [CategoryAdminController::class, 'store']);
        Route::get('/{id}', [CategoryAdminController::class, 'show']);
        Route::put('/{id}', [CategoryAdminController::class, 'update']);
        Route::delete('/{id}', [CategoryAdminController::class, 'destroy']);
    });

    // Brands Management
    Route::prefix('brands')->group(function () {
        Route::get('/', [BrandAdminController::class, 'index']);
        Route::get('/all', [BrandAdminController::class, 'all']);
        Route::post('/', [BrandAdminController::class, 'store']);
        Route::get('/{id}', [BrandAdminController::class, 'show']);
        Route::put('/{id}', [BrandAdminController::class, 'update']);
        Route::delete('/{id}', [BrandAdminController::class, 'destroy']);
    });

    // Products Management
    Route::prefix('products')->group(function () {
        Route::get('/', [ProductAdminController::class, 'index']);
        Route::get('/low-stock', [ProductAdminController::class, 'lowStock']);
        Route::get('/out-of-stock', [ProductAdminController::class, 'outOfStock']);
        Route::post('/', [ProductAdminController::class, 'store']);
        Route::get('/{id}', [ProductAdminController::class, 'show']);
        Route::put('/{id}', [ProductAdminController::class, 'update']);
        Route::delete('/{id}', [ProductAdminController::class, 'destroy']);
    });
});

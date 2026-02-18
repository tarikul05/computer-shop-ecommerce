<?php

use App\Modules\Wishlist\Controllers\WishlistController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Wishlist Module Routes (All Protected)
|--------------------------------------------------------------------------
*/

Route::prefix('wishlist')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [WishlistController::class, 'index']);
    Route::post('/', [WishlistController::class, 'add']);
    Route::post('/toggle', [WishlistController::class, 'toggle']);
    Route::delete('/{productId}', [WishlistController::class, 'remove']);
    Route::get('/check/{productId}', [WishlistController::class, 'check']);
    Route::delete('/', [WishlistController::class, 'clear']);
    Route::get('/count', [WishlistController::class, 'count']);
});

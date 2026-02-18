<?php

use App\Modules\Order\Controllers\OrderController;
use App\Modules\Order\Controllers\Admin\OrderAdminController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Order Module Routes
|--------------------------------------------------------------------------
*/

// Public route for order tracking
Route::get('/orders/track/{orderNumber}', [OrderController::class, 'track']);

// Protected customer routes
Route::prefix('orders')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [OrderController::class, 'index']);
    Route::post('/checkout', [OrderController::class, 'checkout']);
    Route::get('/{id}', [OrderController::class, 'show']);
    Route::get('/number/{orderNumber}', [OrderController::class, 'showByNumber']);
    Route::post('/{id}/cancel', [OrderController::class, 'cancel']);
});

// Admin routes (protected)
Route::prefix('admin/orders')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [OrderAdminController::class, 'index']);
    Route::get('/statistics', [OrderAdminController::class, 'statistics']);
    Route::get('/{id}', [OrderAdminController::class, 'show']);
    Route::patch('/{id}/status', [OrderAdminController::class, 'updateStatus']);
    Route::patch('/{id}/payment-status', [OrderAdminController::class, 'updatePaymentStatus']);
    Route::patch('/{id}/tracking', [OrderAdminController::class, 'updateTracking']);
    Route::post('/{id}/cancel', [OrderAdminController::class, 'cancel']);
});

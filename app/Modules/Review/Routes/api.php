<?php

use App\Modules\Review\Controllers\ReviewController;
use App\Modules\Review\Controllers\Admin\ReviewAdminController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Review Module Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::prefix('products/{productId}/reviews')->group(function () {
    Route::get('/', [ReviewController::class, 'productReviews']);
    Route::get('/summary', [ReviewController::class, 'productRatingSummary']);
});

// Protected customer routes
Route::prefix('reviews')->middleware('auth:sanctum')->group(function () {
    Route::get('/my', [ReviewController::class, 'myReviews']);
    Route::get('/can-review/{productId}', [ReviewController::class, 'canReview']);
    Route::post('/', [ReviewController::class, 'store']);
    Route::put('/{id}', [ReviewController::class, 'update']);
    Route::delete('/{id}', [ReviewController::class, 'destroy']);
    Route::post('/{id}/vote', [ReviewController::class, 'vote']);
});

// Admin routes (protected)
Route::prefix('admin/reviews')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [ReviewAdminController::class, 'index']);
    Route::get('/{id}', [ReviewAdminController::class, 'show']);
    Route::post('/{id}/approve', [ReviewAdminController::class, 'approve']);
    Route::post('/{id}/reject', [ReviewAdminController::class, 'reject']);
    Route::post('/{id}/respond', [ReviewAdminController::class, 'respond']);
    Route::delete('/{id}', [ReviewAdminController::class, 'destroy']);
});

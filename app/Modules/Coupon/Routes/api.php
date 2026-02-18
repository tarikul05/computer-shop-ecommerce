<?php

use App\Modules\Coupon\Controllers\Admin\CouponAdminController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Coupon Module Routes
|--------------------------------------------------------------------------
*/

// Admin routes (protected)
Route::prefix('admin/coupons')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [CouponAdminController::class, 'index']);
    Route::post('/', [CouponAdminController::class, 'store']);
    Route::get('/{id}', [CouponAdminController::class, 'show']);
    Route::put('/{id}', [CouponAdminController::class, 'update']);
    Route::delete('/{id}', [CouponAdminController::class, 'destroy']);
});

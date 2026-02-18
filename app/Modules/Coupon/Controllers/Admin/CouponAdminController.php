<?php

namespace App\Modules\Coupon\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\Coupon\Services\CouponService;
use App\Modules\Coupon\Requests\StoreCouponRequest;
use App\Modules\Coupon\Requests\UpdateCouponRequest;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CouponAdminController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly CouponService $couponService
    ) {}

    /**
     * Get paginated coupons
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->input('per_page', 15);
        $coupons = $this->couponService->getPaginated($perPage);

        return $this->paginatedResponse($coupons);
    }

    /**
     * Get coupon by ID
     */
    public function show(int $id): JsonResponse
    {
        $coupon = $this->couponService->getById($id);

        if (!$coupon) {
            return $this->notFoundResponse('Coupon not found');
        }

        return $this->successResponse($coupon);
    }

    /**
     * Create coupon
     */
    public function store(StoreCouponRequest $request): JsonResponse
    {
        $coupon = $this->couponService->create($request->validated());

        return $this->createdResponse($coupon, 'Coupon created successfully');
    }

    /**
     * Update coupon
     */
    public function update(UpdateCouponRequest $request, int $id): JsonResponse
    {
        $coupon = $this->couponService->getById($id);

        if (!$coupon) {
            return $this->notFoundResponse('Coupon not found');
        }

        $coupon = $this->couponService->update($coupon, $request->validated());

        return $this->successResponse($coupon, 'Coupon updated successfully');
    }

    /**
     * Delete coupon
     */
    public function destroy(int $id): JsonResponse
    {
        $coupon = $this->couponService->getById($id);

        if (!$coupon) {
            return $this->notFoundResponse('Coupon not found');
        }

        $this->couponService->delete($coupon);

        return $this->successResponse(null, 'Coupon deleted successfully');
    }
}

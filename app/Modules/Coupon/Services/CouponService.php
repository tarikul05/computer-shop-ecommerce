<?php

namespace App\Modules\Coupon\Services;

use App\Modules\Coupon\Models\Coupon;
use App\Modules\Coupon\Repositories\CouponRepository;
use App\Modules\User\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

class CouponService
{
    public function __construct(
        private readonly CouponRepository $couponRepository
    ) {}

    /**
     * Get paginated coupons (admin)
     */
    public function getPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return $this->couponRepository->paginate($perPage);
    }

    /**
     * Get coupon by ID
     */
    public function getById(int $id): ?Coupon
    {
        return $this->couponRepository->findById($id);
    }

    /**
     * Validate coupon
     */
    public function validateCoupon(string $code, float $subtotal, ?User $user = null): ?Coupon
    {
        $coupon = $this->couponRepository->findValidByCode($code);

        if (!$coupon) {
            return null;
        }

        // Check minimum order amount
        if ($coupon->min_order_amount && $subtotal < $coupon->min_order_amount) {
            return null;
        }

        // TODO: Check per-user usage limit if user is authenticated
        // This would require tracking coupon usage per user

        return $coupon;
    }

    /**
     * Create coupon
     */
    public function create(array $data): Coupon
    {
        return $this->couponRepository->create($data);
    }

    /**
     * Update coupon
     */
    public function update(Coupon $coupon, array $data): Coupon
    {
        return $this->couponRepository->update($coupon, $data);
    }

    /**
     * Delete coupon
     */
    public function delete(Coupon $coupon): bool
    {
        return $this->couponRepository->delete($coupon);
    }

    /**
     * Increment coupon usage
     */
    public function incrementUsage(Coupon $coupon): void
    {
        $coupon->incrementUsage();
    }
}

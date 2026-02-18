<?php

namespace App\Modules\Coupon\Repositories;

use App\Modules\Coupon\Models\Coupon;
use Illuminate\Pagination\LengthAwarePaginator;

class CouponRepository
{
    /**
     * Get all coupons paginated
     */
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return Coupon::latest()->paginate($perPage);
    }

    /**
     * Find coupon by ID
     */
    public function findById(int $id): ?Coupon
    {
        return Coupon::find($id);
    }

    /**
     * Find coupon by code
     */
    public function findByCode(string $code): ?Coupon
    {
        return Coupon::where('code', strtoupper($code))->first();
    }

    /**
     * Find valid coupon by code
     */
    public function findValidByCode(string $code): ?Coupon
    {
        return Coupon::valid()
            ->where('code', strtoupper($code))
            ->first();
    }

    /**
     * Create coupon
     */
    public function create(array $data): Coupon
    {
        $data['code'] = strtoupper($data['code']);
        return Coupon::create($data);
    }

    /**
     * Update coupon
     */
    public function update(Coupon $coupon, array $data): Coupon
    {
        if (isset($data['code'])) {
            $data['code'] = strtoupper($data['code']);
        }
        $coupon->update($data);
        return $coupon->fresh();
    }

    /**
     * Delete coupon
     */
    public function delete(Coupon $coupon): bool
    {
        return $coupon->delete();
    }
}

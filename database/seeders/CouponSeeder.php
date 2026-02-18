<?php

namespace Database\Seeders;

use App\Modules\Coupon\Models\Coupon;
use Illuminate\Database\Seeder;

class CouponSeeder extends Seeder
{
    public function run(): void
    {
        $coupons = [
            [
                'code' => 'WELCOME10',
                'description' => '10% off for new customers',
                'type' => 'percentage',
                'value' => 10,
                'min_order_amount' => 1000,
                'max_discount_amount' => 5000,
                'usage_limit' => null,
                'usage_limit_per_user' => 1,
                'starts_at' => now(),
                'expires_at' => now()->addYear(),
                'is_active' => true,
            ],
            [
                'code' => 'FLAT500',
                'description' => 'Flat ৳500 off on orders above ৳5000',
                'type' => 'fixed',
                'value' => 500,
                'min_order_amount' => 5000,
                'max_discount_amount' => null,
                'usage_limit' => 1000,
                'usage_limit_per_user' => 3,
                'starts_at' => now(),
                'expires_at' => now()->addMonths(6),
                'is_active' => true,
            ],
            [
                'code' => 'SUMMER15',
                'description' => '15% off summer sale',
                'type' => 'percentage',
                'value' => 15,
                'min_order_amount' => 3000,
                'max_discount_amount' => 10000,
                'usage_limit' => 500,
                'usage_limit_per_user' => 2,
                'starts_at' => now(),
                'expires_at' => now()->addMonths(3),
                'is_active' => true,
            ],
            [
                'code' => 'GPU20',
                'description' => '20% off on Graphics Cards',
                'type' => 'percentage',
                'value' => 20,
                'min_order_amount' => 30000,
                'max_discount_amount' => 20000,
                'usage_limit' => 100,
                'usage_limit_per_user' => 1,
                'starts_at' => now(),
                'expires_at' => now()->addMonth(),
                'is_active' => true,
            ],
            [
                'code' => 'FLAT1000',
                'description' => 'Flat ৳1000 off on orders above ৳15000',
                'type' => 'fixed',
                'value' => 1000,
                'min_order_amount' => 15000,
                'max_discount_amount' => null,
                'usage_limit' => 200,
                'usage_limit_per_user' => 1,
                'starts_at' => now(),
                'expires_at' => now()->addMonths(2),
                'is_active' => true,
            ],
            [
                'code' => 'MEGA25',
                'description' => '25% off mega sale (max ৳15000)',
                'type' => 'percentage',
                'value' => 25,
                'min_order_amount' => 20000,
                'max_discount_amount' => 15000,
                'usage_limit' => 50,
                'usage_limit_per_user' => 1,
                'starts_at' => now(),
                'expires_at' => now()->addWeeks(2),
                'is_active' => true,
            ],
            [
                'code' => 'LAPTOP5000',
                'description' => 'Flat ৳5000 off on Laptops above ৳80000',
                'type' => 'fixed',
                'value' => 5000,
                'min_order_amount' => 80000,
                'max_discount_amount' => null,
                'usage_limit' => 30,
                'usage_limit_per_user' => 1,
                'starts_at' => now(),
                'expires_at' => now()->addMonth(),
                'is_active' => true,
            ],
            [
                'code' => 'FREESHIP',
                'description' => 'Free shipping (৳150 off)',
                'type' => 'fixed',
                'value' => 150,
                'min_order_amount' => 2000,
                'max_discount_amount' => null,
                'usage_limit' => null,
                'usage_limit_per_user' => 5,
                'starts_at' => now(),
                'expires_at' => now()->addYear(),
                'is_active' => true,
            ],
            // Expired coupon for testing
            [
                'code' => 'EXPIRED20',
                'description' => 'Expired 20% discount',
                'type' => 'percentage',
                'value' => 20,
                'min_order_amount' => 1000,
                'max_discount_amount' => 5000,
                'usage_limit' => 100,
                'usage_limit_per_user' => 1,
                'starts_at' => now()->subMonths(2),
                'expires_at' => now()->subMonth(),
                'is_active' => true,
            ],
            // Inactive coupon for testing
            [
                'code' => 'INACTIVE50',
                'description' => 'Inactive 50% discount',
                'type' => 'percentage',
                'value' => 50,
                'min_order_amount' => 1000,
                'max_discount_amount' => 25000,
                'usage_limit' => 10,
                'usage_limit_per_user' => 1,
                'starts_at' => now(),
                'expires_at' => now()->addYear(),
                'is_active' => false,
            ],
        ];

        foreach ($coupons as $coupon) {
            Coupon::create($coupon);
        }

        $this->command->info('Coupons seeded: ' . count($coupons) . ' total');
    }
}

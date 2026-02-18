<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('🚀 Starting database seeding...');
        $this->command->newLine();

        // Run seeders in correct order (respecting foreign key dependencies)
        $this->call([
            // 1. Users and Addresses first (no dependencies)
            UserSeeder::class,
            
            // 2. Categories and Brands (no dependencies)
            CategorySeeder::class,
            BrandSeeder::class,
            
            // 3. Products (depends on Categories, Brands)
            ProductSeeder::class,
            
            // 4. Coupons (no dependencies)
            CouponSeeder::class,
            
            // 5. Orders (depends on Users, Products, Coupons)
            OrderSeeder::class,
            
            // 6. Reviews (depends on Users, Products)
            ReviewSeeder::class,
            
            // 7. Wishlists (depends on Users, Products)
            WishlistSeeder::class,
            
            // 8. Carts (depends on Users, Products)
            CartSeeder::class,
        ]);

        $this->command->newLine();
        $this->command->info('✅ Database seeding completed successfully!');
        $this->command->newLine();
        $this->command->table(
            ['Resource', 'Count'],
            [
                ['Users', \App\Models\User::count()],
                ['Categories', \App\Modules\Product\Models\Category::count()],
                ['Brands', \App\Modules\Product\Models\Brand::count()],
                ['Products', \App\Modules\Product\Models\Product::count()],
                ['Coupons', \App\Modules\Coupon\Models\Coupon::count()],
                ['Orders', \App\Modules\Order\Models\Order::count()],
                ['Reviews', \App\Modules\Review\Models\Review::count()],
                ['Wishlists', \App\Modules\Wishlist\Models\Wishlist::count()],
                ['Carts', \App\Modules\Cart\Models\Cart::count()],
            ]
        );
        $this->command->newLine();
        $this->command->info('📧 Admin Login: admin@computerstore.com / password');
        $this->command->info('📧 Customer Login: rahim@example.com / password');
    }
}

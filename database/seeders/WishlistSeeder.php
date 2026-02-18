<?php

namespace Database\Seeders;

use App\Models\User;
use App\Modules\Product\Models\Product;
use App\Modules\Wishlist\Models\Wishlist;
use Illuminate\Database\Seeder;

class WishlistSeeder extends Seeder
{
    public function run(): void
    {
        $customers = User::where('role', 'customer')->get();
        $products = Product::all();

        if ($customers->isEmpty() || $products->isEmpty()) {
            $this->command->warn('Please run UserSeeder and ProductSeeder first');
            return;
        }

        $wishlistCount = 0;

        // Each customer gets 0-10 wishlist items
        foreach ($customers as $customer) {
            $numItems = rand(0, 10);
            $wishlistProducts = $products->random(min($numItems, $products->count()));

            foreach ($wishlistProducts as $product) {
                // Check if already in wishlist
                $exists = Wishlist::where('user_id', $customer->id)
                    ->where('product_id', $product->id)
                    ->exists();

                if ($exists) {
                    continue;
                }

                Wishlist::create([
                    'user_id' => $customer->id,
                    'product_id' => $product->id,
                    'created_at' => now()->subDays(rand(1, 90)),
                ]);

                $wishlistCount++;
            }
        }

        $this->command->info("Wishlist items seeded: {$wishlistCount} total");
    }
}

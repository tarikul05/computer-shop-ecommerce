<?php

namespace Database\Seeders;

use App\Models\User;
use App\Modules\Product\Models\Product;
use App\Modules\Cart\Models\Cart;
use App\Modules\Cart\Models\CartItem;
use Illuminate\Database\Seeder;

class CartSeeder extends Seeder
{
    public function run(): void
    {
        $customers = User::where('role', 'customer')->get();
        $products = Product::where('is_active', true)->get();

        if ($customers->isEmpty() || $products->isEmpty()) {
            $this->command->warn('Please run UserSeeder and ProductSeeder first');
            return;
        }

        $cartCount = 0;

        // Some customers (about 60%) have items in cart
        foreach ($customers as $customer) {
            if (rand(1, 10) > 6) {
                continue; // 40% of customers have empty carts
            }

            // Create or get cart
            $cart = Cart::firstOrCreate(
                ['user_id' => $customer->id],
                ['session_id' => null]
            );

            // Add 1-5 random products
            $numItems = rand(1, 5);
            $cartProducts = $products->random(min($numItems, $products->count()));

            foreach ($cartProducts as $product) {
                // Check if product already in cart
                $existingItem = CartItem::where('cart_id', $cart->id)
                    ->where('product_id', $product->id)
                    ->first();

                if ($existingItem) {
                    continue;
                }

                CartItem::create([
                    'cart_id' => $cart->id,
                    'product_id' => $product->id,
                    'quantity' => rand(1, 3),
                    'price' => $product->price,
                ]);
            }

            $cartCount++;
        }

        // Also create some guest carts (session-based)
        for ($i = 0; $i < 5; $i++) {
            $sessionId = 'guest_' . uniqid();
            
            $cart = Cart::create([
                'user_id' => null,
                'session_id' => $sessionId,
            ]);

            $numItems = rand(1, 3);
            $cartProducts = $products->random(min($numItems, $products->count()));

            foreach ($cartProducts as $product) {
                CartItem::create([
                    'cart_id' => $cart->id,
                    'product_id' => $product->id,
                    'quantity' => rand(1, 2),
                    'price' => $product->price,
                ]);
            }

            $cartCount++;
        }

        $this->command->info("Carts seeded: {$cartCount} total (including " . ($cartCount > 5 ? $cartCount - 5 : 0) . " user carts and 5 guest carts)");
    }
}

<?php

namespace App\Modules\Cart\Repositories;

use App\Modules\Cart\Models\Cart;
use App\Modules\Cart\Models\CartItem;
use App\Modules\User\Models\User;

class CartRepository
{
    /**
     * Get or create cart for user/session
     */
    public function getOrCreate(?User $user, ?string $sessionId): Cart
    {
        if ($user) {
            $cart = Cart::where('user_id', $user->id)->first();
            
            if (!$cart) {
                // Check if there's a session cart to merge
                if ($sessionId) {
                    $cart = Cart::where('session_id', $sessionId)->first();
                    if ($cart) {
                        $cart->update(['user_id' => $user->id, 'session_id' => null]);
                    }
                }
            }
            
            if (!$cart) {
                $cart = Cart::create(['user_id' => $user->id]);
            }
        } else {
            $cart = Cart::where('session_id', $sessionId)->first();
            
            if (!$cart) {
                $cart = Cart::create(['session_id' => $sessionId]);
            }
        }

        return $cart->load('items.product.primaryImage');
    }

    /**
     * Find cart by ID
     */
    public function findById(int $id): ?Cart
    {
        return Cart::with('items.product.primaryImage')->find($id);
    }

    /**
     * Add item to cart
     */
    public function addItem(Cart $cart, int $productId, int $quantity, float $price): CartItem
    {
        $existingItem = $cart->items()->where('product_id', $productId)->first();

        if ($existingItem) {
            $existingItem->update([
                'quantity' => $existingItem->quantity + $quantity,
            ]);
            return $existingItem->fresh();
        }

        return $cart->items()->create([
            'product_id' => $productId,
            'quantity' => $quantity,
            'price' => $price,
        ]);
    }

    /**
     * Update item quantity
     */
    public function updateItemQuantity(CartItem $item, int $quantity): CartItem
    {
        $item->update(['quantity' => $quantity]);
        return $item->fresh();
    }

    /**
     * Remove item from cart
     */
    public function removeItem(CartItem $item): void
    {
        $item->delete();
    }

    /**
     * Clear cart items
     */
    public function clearItems(Cart $cart): void
    {
        $cart->items()->delete();
        $cart->update([
            'coupon_id' => null,
            'coupon_code' => null,
            'discount_amount' => 0,
        ]);
    }

    /**
     * Apply coupon to cart
     */
    public function applyCoupon(Cart $cart, int $couponId, string $code, float $discount): Cart
    {
        $cart->update([
            'coupon_id' => $couponId,
            'coupon_code' => $code,
            'discount_amount' => $discount,
        ]);

        return $cart->fresh();
    }

    /**
     * Remove coupon from cart
     */
    public function removeCoupon(Cart $cart): Cart
    {
        $cart->update([
            'coupon_id' => null,
            'coupon_code' => null,
            'discount_amount' => 0,
        ]);

        return $cart->fresh();
    }

    /**
     * Merge session cart to user cart
     */
    public function mergeSessionCart(User $user, string $sessionId): ?Cart
    {
        $sessionCart = Cart::where('session_id', $sessionId)->first();
        
        if (!$sessionCart) {
            return null;
        }

        $userCart = $this->getOrCreate($user, null);

        foreach ($sessionCart->items as $item) {
            $existingItem = $userCart->items()->where('product_id', $item->product_id)->first();
            
            if ($existingItem) {
                $existingItem->update([
                    'quantity' => $existingItem->quantity + $item->quantity,
                ]);
            } else {
                $userCart->items()->create([
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                ]);
            }
        }

        $sessionCart->delete();

        return $userCart->fresh('items.product.primaryImage');
    }
}

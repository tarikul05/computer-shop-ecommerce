<?php

namespace App\Modules\Cart\Services;

use App\Modules\Cart\Models\Cart;
use App\Modules\Cart\Models\CartItem;
use App\Modules\Cart\Repositories\CartRepository;
use App\Modules\Product\Repositories\ProductRepository;
use App\Modules\Coupon\Services\CouponService;
use App\Modules\User\Models\User;
use Illuminate\Support\Str;

class CartService
{
    public function __construct(
        private readonly CartRepository $cartRepository,
        private readonly ProductRepository $productRepository,
        private readonly CouponService $couponService
    ) {}

    /**
     * Get cart for current user/session
     */
    public function getCart(?User $user, ?string $sessionId = null): array
    {
        $sessionId = $sessionId ?? $this->generateSessionId();
        $cart = $this->cartRepository->getOrCreate($user, $sessionId);

        return $this->formatCart($cart);
    }

    /**
     * Add item to cart
     */
    public function addItem(?User $user, ?string $sessionId, int $productId, int $quantity = 1): array
    {
        $product = $this->productRepository->findById($productId);

        if (!$product || !$product->is_active) {
            throw new \Exception('Product not found or unavailable');
        }

        if (!$product->is_in_stock) {
            throw new \Exception('Product is out of stock');
        }

        if ($product->quantity < $quantity) {
            throw new \Exception("Only {$product->quantity} items available");
        }

        $sessionId = $sessionId ?? $this->generateSessionId();
        $cart = $this->cartRepository->getOrCreate($user, $sessionId);

        // Check if adding to existing item exceeds stock
        $existingItem = $cart->items()->where('product_id', $productId)->first();
        $totalQuantity = ($existingItem?->quantity ?? 0) + $quantity;
        
        if ($totalQuantity > $product->quantity) {
            throw new \Exception("Cannot add more items. Only {$product->quantity} available");
        }

        $this->cartRepository->addItem($cart, $productId, $quantity, $product->price);

        // Recalculate coupon discount if applied
        if ($cart->coupon_id) {
            $this->recalculateCouponDiscount($cart);
        }

        return $this->formatCart($cart->fresh('items.product.primaryImage'));
    }

    /**
     * Update item quantity
     */
    public function updateQuantity(?User $user, ?string $sessionId, int $itemId, int $quantity): array
    {
        $cart = $this->cartRepository->getOrCreate($user, $sessionId);
        $item = $cart->items()->find($itemId);

        if (!$item) {
            throw new \Exception('Item not found in cart');
        }

        if ($quantity <= 0) {
            $this->cartRepository->removeItem($item);
        } else {
            $product = $item->product;
            
            if ($quantity > $product->quantity) {
                throw new \Exception("Only {$product->quantity} items available");
            }

            $this->cartRepository->updateItemQuantity($item, $quantity);
        }

        // Recalculate coupon discount if applied
        if ($cart->coupon_id) {
            $this->recalculateCouponDiscount($cart);
        }

        return $this->formatCart($cart->fresh('items.product.primaryImage'));
    }

    /**
     * Remove item from cart
     */
    public function removeItem(?User $user, ?string $sessionId, int $itemId): array
    {
        $cart = $this->cartRepository->getOrCreate($user, $sessionId);
        $item = $cart->items()->find($itemId);

        if (!$item) {
            throw new \Exception('Item not found in cart');
        }

        $this->cartRepository->removeItem($item);

        // Recalculate coupon discount if applied
        if ($cart->coupon_id) {
            $this->recalculateCouponDiscount($cart);
        }

        return $this->formatCart($cart->fresh('items.product.primaryImage'));
    }

    /**
     * Clear cart
     */
    public function clearCart(?User $user, ?string $sessionId): array
    {
        $cart = $this->cartRepository->getOrCreate($user, $sessionId);
        $this->cartRepository->clearItems($cart);

        return $this->formatCart($cart->fresh('items.product.primaryImage'));
    }

    /**
     * Apply coupon to cart
     */
    public function applyCoupon(?User $user, ?string $sessionId, string $code): array
    {
        $cart = $this->cartRepository->getOrCreate($user, $sessionId);

        if ($cart->is_empty) {
            throw new \Exception('Cart is empty');
        }

        $coupon = $this->couponService->validateCoupon($code, $cart->subtotal, $user);

        if (!$coupon) {
            throw new \Exception('Invalid or expired coupon');
        }

        $discount = $coupon->calculateDiscount($cart->subtotal);

        $this->cartRepository->applyCoupon($cart, $coupon->id, $coupon->code, $discount);

        return $this->formatCart($cart->fresh('items.product.primaryImage'));
    }

    /**
     * Remove coupon from cart
     */
    public function removeCoupon(?User $user, ?string $sessionId): array
    {
        $cart = $this->cartRepository->getOrCreate($user, $sessionId);
        $this->cartRepository->removeCoupon($cart);

        return $this->formatCart($cart->fresh('items.product.primaryImage'));
    }

    /**
     * Merge session cart to user cart after login
     */
    public function mergeCartOnLogin(User $user, string $sessionId): array
    {
        $cart = $this->cartRepository->mergeSessionCart($user, $sessionId);
        
        if (!$cart) {
            $cart = $this->cartRepository->getOrCreate($user, null);
        }

        return $this->formatCart($cart);
    }

    /**
     * Recalculate coupon discount
     */
    protected function recalculateCouponDiscount(Cart $cart): void
    {
        $cart->refresh();
        
        if (!$cart->coupon_id || $cart->is_empty) {
            $this->cartRepository->removeCoupon($cart);
            return;
        }

        $coupon = $cart->coupon;
        
        if (!$coupon || !$coupon->isValid()) {
            $this->cartRepository->removeCoupon($cart);
            return;
        }

        $discount = $coupon->calculateDiscount($cart->subtotal);
        
        $cart->update(['discount_amount' => $discount]);
    }

    /**
     * Generate session ID for guest cart
     */
    protected function generateSessionId(): string
    {
        return Str::uuid()->toString();
    }

    /**
     * Format cart for response
     */
    protected function formatCart(Cart $cart): array
    {
        return [
            'id' => $cart->id,
            'items' => $cart->items->map(fn($item) => [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'product' => [
                    'id' => $item->product->id,
                    'name' => $item->product->name,
                    'slug' => $item->product->slug,
                    'price' => $item->product->price,
                    'image' => $item->product->primary_image_url,
                    'is_in_stock' => $item->product->is_in_stock,
                    'available_quantity' => $item->product->quantity,
                ],
                'quantity' => $item->quantity,
                'price' => $item->price,
                'total' => $item->total,
            ]),
            'items_count' => $cart->items_count,
            'subtotal' => $cart->subtotal,
            'coupon' => $cart->coupon_code ? [
                'code' => $cart->coupon_code,
                'discount' => $cart->discount_amount,
            ] : null,
            'discount' => $cart->discount_amount,
            'total' => $cart->total,
            'is_empty' => $cart->is_empty,
        ];
    }
}

<?php

namespace App\Modules\Cart\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Cart\Services\CartService;
use App\Modules\Cart\Requests\AddToCartRequest;
use App\Modules\Cart\Requests\UpdateCartItemRequest;
use App\Modules\Cart\Requests\ApplyCouponRequest;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly CartService $cartService
    ) {}

    /**
     * Get cart
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $cart = $this->cartService->getCart(
                $request->user(),
                $request->header('X-Cart-Session')
            );

            return $this->successResponse($cart);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Add item to cart
     */
    public function add(AddToCartRequest $request): JsonResponse
    {
        try {
            $cart = $this->cartService->addItem(
                $request->user(),
                $request->header('X-Cart-Session'),
                $request->product_id,
                $request->quantity ?? 1
            );

            return $this->successResponse($cart, 'Item added to cart');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Update item quantity
     */
    public function update(UpdateCartItemRequest $request, int $itemId): JsonResponse
    {
        try {
            $cart = $this->cartService->updateQuantity(
                $request->user(),
                $request->header('X-Cart-Session'),
                $itemId,
                $request->quantity
            );

            return $this->successResponse($cart, 'Cart updated');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Remove item from cart
     */
    public function remove(Request $request, int $itemId): JsonResponse
    {
        try {
            $cart = $this->cartService->removeItem(
                $request->user(),
                $request->header('X-Cart-Session'),
                $itemId
            );

            return $this->successResponse($cart, 'Item removed from cart');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Clear cart
     */
    public function clear(Request $request): JsonResponse
    {
        try {
            $cart = $this->cartService->clearCart(
                $request->user(),
                $request->header('X-Cart-Session')
            );

            return $this->successResponse($cart, 'Cart cleared');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Apply coupon
     */
    public function applyCoupon(ApplyCouponRequest $request): JsonResponse
    {
        try {
            $cart = $this->cartService->applyCoupon(
                $request->user(),
                $request->header('X-Cart-Session'),
                $request->code
            );

            return $this->successResponse($cart, 'Coupon applied');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Remove coupon
     */
    public function removeCoupon(Request $request): JsonResponse
    {
        try {
            $cart = $this->cartService->removeCoupon(
                $request->user(),
                $request->header('X-Cart-Session')
            );

            return $this->successResponse($cart, 'Coupon removed');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Merge cart after login
     */
    public function merge(Request $request): JsonResponse
    {
        try {
            $sessionId = $request->header('X-Cart-Session');
            
            if (!$sessionId) {
                return $this->successResponse($this->cartService->getCart($request->user(), null));
            }

            $cart = $this->cartService->mergeCartOnLogin($request->user(), $sessionId);

            return $this->successResponse($cart, 'Cart merged');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }
}

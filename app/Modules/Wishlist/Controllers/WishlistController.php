<?php

namespace App\Modules\Wishlist\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Wishlist\Services\WishlistService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly WishlistService $wishlistService
    ) {}

    /**
     * Get wishlist
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $wishlist = $this->wishlistService->getWishlist($request->user());

            return $this->successResponse($wishlist);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Add to wishlist
     */
    public function add(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        try {
            $result = $this->wishlistService->addToWishlist(
                $request->user(),
                $request->product_id
            );

            return $this->successResponse($result);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Remove from wishlist
     */
    public function remove(Request $request, int $productId): JsonResponse
    {
        try {
            $result = $this->wishlistService->removeFromWishlist(
                $request->user(),
                $productId
            );

            return $this->successResponse($result);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Toggle wishlist
     */
    public function toggle(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        try {
            $result = $this->wishlistService->toggle(
                $request->user(),
                $request->product_id
            );

            return $this->successResponse($result);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Check if product is in wishlist
     */
    public function check(Request $request, int $productId): JsonResponse
    {
        $inWishlist = $this->wishlistService->isInWishlist(
            $request->user(),
            $productId
        );

        return $this->successResponse(['in_wishlist' => $inWishlist]);
    }

    /**
     * Clear wishlist
     */
    public function clear(Request $request): JsonResponse
    {
        try {
            $result = $this->wishlistService->clearWishlist($request->user());

            return $this->successResponse($result);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Get wishlist count
     */
    public function count(Request $request): JsonResponse
    {
        $count = $this->wishlistService->getCount($request->user());

        return $this->successResponse(['count' => $count]);
    }
}

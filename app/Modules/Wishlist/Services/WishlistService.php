<?php

namespace App\Modules\Wishlist\Services;

use App\Modules\Wishlist\Repositories\WishlistRepository;
use App\Modules\Product\Repositories\ProductRepository;
use App\Modules\Product\Services\ProductService;
use App\Modules\User\Models\User;
use Illuminate\Support\Collection;

class WishlistService
{
    public function __construct(
        private readonly WishlistRepository $wishlistRepository,
        private readonly ProductRepository $productRepository,
        private readonly ProductService $productService
    ) {}

    /**
     * Get user's wishlist
     */
    public function getWishlist(User $user): array
    {
        $items = $this->wishlistRepository->getByUser($user);

        return [
            'items' => $items->map(fn($item) => [
                'id' => $item->id,
                'added_at' => $item->created_at,
                'product' => $this->productService->formatProductList($item->product),
            ]),
            'count' => $items->count(),
        ];
    }

    /**
     * Add product to wishlist
     */
    public function addToWishlist(User $user, int $productId): array
    {
        $product = $this->productRepository->findById($productId);

        if (!$product || !$product->is_active) {
            throw new \Exception('Product not found');
        }

        $this->wishlistRepository->add($user, $productId);

        return [
            'message' => 'Product added to wishlist',
            'count' => $this->wishlistRepository->count($user),
        ];
    }

    /**
     * Remove product from wishlist
     */
    public function removeFromWishlist(User $user, int $productId): array
    {
        $removed = $this->wishlistRepository->remove($user, $productId);

        if (!$removed) {
            throw new \Exception('Product not found in wishlist');
        }

        return [
            'message' => 'Product removed from wishlist',
            'count' => $this->wishlistRepository->count($user),
        ];
    }

    /**
     * Toggle product in wishlist
     */
    public function toggle(User $user, int $productId): array
    {
        if ($this->wishlistRepository->exists($user, $productId)) {
            return $this->removeFromWishlist($user, $productId);
        }

        return $this->addToWishlist($user, $productId);
    }

    /**
     * Check if product is in wishlist
     */
    public function isInWishlist(User $user, int $productId): bool
    {
        return $this->wishlistRepository->exists($user, $productId);
    }

    /**
     * Clear wishlist
     */
    public function clearWishlist(User $user): array
    {
        $this->wishlistRepository->clear($user);

        return [
            'message' => 'Wishlist cleared',
            'count' => 0,
        ];
    }

    /**
     * Get wishlist count
     */
    public function getCount(User $user): int
    {
        return $this->wishlistRepository->count($user);
    }
}

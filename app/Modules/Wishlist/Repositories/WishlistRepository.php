<?php

namespace App\Modules\Wishlist\Repositories;

use App\Modules\Wishlist\Models\Wishlist;
use App\Modules\User\Models\User;
use Illuminate\Support\Collection;

class WishlistRepository
{
    /**
     * Get user's wishlist
     */
    public function getByUser(User $user): Collection
    {
        return Wishlist::where('user_id', $user->id)
            ->with(['product.primaryImage', 'product.brand', 'product.category'])
            ->latest()
            ->get();
    }

    /**
     * Check if product is in wishlist
     */
    public function exists(User $user, int $productId): bool
    {
        return Wishlist::where('user_id', $user->id)
            ->where('product_id', $productId)
            ->exists();
    }

    /**
     * Add to wishlist
     */
    public function add(User $user, int $productId): Wishlist
    {
        return Wishlist::firstOrCreate([
            'user_id' => $user->id,
            'product_id' => $productId,
        ]);
    }

    /**
     * Remove from wishlist
     */
    public function remove(User $user, int $productId): bool
    {
        return Wishlist::where('user_id', $user->id)
            ->where('product_id', $productId)
            ->delete() > 0;
    }

    /**
     * Clear wishlist
     */
    public function clear(User $user): void
    {
        Wishlist::where('user_id', $user->id)->delete();
    }

    /**
     * Get wishlist count
     */
    public function count(User $user): int
    {
        return Wishlist::where('user_id', $user->id)->count();
    }
}

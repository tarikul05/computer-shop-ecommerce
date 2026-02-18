<?php

namespace App\Modules\Review\Repositories;

use App\Modules\Review\Models\Review;
use App\Modules\Review\Models\ReviewVote;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

class ReviewRepository
{
    /**
     * Get reviews for product (approved only)
     */
    public function getByProduct(int $productId, array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = Review::with(['user', 'images'])
            ->where('product_id', $productId)
            ->approved();

        // Filter by rating
        if (!empty($filters['rating'])) {
            $query->where('rating', $filters['rating']);
        }

        // Filter verified only
        if (!empty($filters['verified_only'])) {
            $query->verified();
        }

        // Filter with images only
        if (!empty($filters['with_images'])) {
            $query->has('images');
        }

        // Sorting
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortDir = $filters['sort_dir'] ?? 'desc';

        if ($sortBy === 'helpful') {
            $query->orderBy('helpful_count', 'desc');
        } elseif ($sortBy === 'rating_high') {
            $query->orderBy('rating', 'desc');
        } elseif ($sortBy === 'rating_low') {
            $query->orderBy('rating', 'asc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        return $query->paginate($perPage);
    }

    /**
     * Get all reviews (admin)
     */
    public function getPaginated(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Review::with(['user', 'product', 'images']);

        // Filter by status
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Filter by product
        if (!empty($filters['product_id'])) {
            $query->where('product_id', $filters['product_id']);
        }

        // Filter by rating
        if (!empty($filters['rating'])) {
            $query->where('rating', $filters['rating']);
        }

        // Search
        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('title', 'like', "%{$filters['search']}%")
                  ->orWhere('comment', 'like', "%{$filters['search']}%");
            });
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    /**
     * Get user reviews
     */
    public function getByUser(User $user, int $perPage = 10): LengthAwarePaginator
    {
        return Review::with(['product', 'images'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get review by ID
     */
    public function getById(int $id): ?Review
    {
        return Review::with(['user', 'product', 'images'])->find($id);
    }

    /**
     * Check if user has reviewed product
     */
    public function hasUserReviewed(int $userId, int $productId): bool
    {
        return Review::where('user_id', $userId)
            ->where('product_id', $productId)
            ->exists();
    }

    /**
     * Get user's review for product
     */
    public function getUserReview(int $userId, int $productId): ?Review
    {
        return Review::with('images')
            ->where('user_id', $userId)
            ->where('product_id', $productId)
            ->first();
    }

    /**
     * Create review
     */
    public function create(array $data): Review
    {
        return Review::create($data);
    }

    /**
     * Update review
     */
    public function update(Review $review, array $data): Review
    {
        $review->update($data);
        return $review->fresh();
    }

    /**
     * Delete review
     */
    public function delete(Review $review): bool
    {
        return $review->delete();
    }

    /**
     * Add review image
     */
    public function addImage(Review $review, string $imagePath, int $sortOrder = 0): void
    {
        $review->images()->create([
            'image' => $imagePath,
            'sort_order' => $sortOrder,
        ]);
    }

    /**
     * Vote on review
     */
    public function vote(Review $review, int $userId, bool $isHelpful): ReviewVote
    {
        $existingVote = $review->getUserVote($userId);

        if ($existingVote) {
            // Update vote if changed
            if ($existingVote->is_helpful !== $isHelpful) {
                // Adjust counts
                if ($isHelpful) {
                    $review->increment('helpful_count');
                    $review->decrement('not_helpful_count');
                } else {
                    $review->decrement('helpful_count');
                    $review->increment('not_helpful_count');
                }
                
                $existingVote->update(['is_helpful' => $isHelpful]);
            }
            return $existingVote;
        }

        // Create new vote
        $vote = $review->votes()->create([
            'user_id' => $userId,
            'is_helpful' => $isHelpful,
        ]);

        // Update count
        if ($isHelpful) {
            $review->increment('helpful_count');
        } else {
            $review->increment('not_helpful_count');
        }

        return $vote;
    }

    /**
     * Get product rating summary
     */
    public function getProductRatingSummary(int $productId): array
    {
        $reviews = Review::where('product_id', $productId)
            ->approved()
            ->selectRaw('rating, COUNT(*) as count')
            ->groupBy('rating')
            ->get()
            ->pluck('count', 'rating')
            ->toArray();

        $totalReviews = array_sum($reviews);
        $averageRating = $totalReviews > 0 
            ? Review::where('product_id', $productId)->approved()->avg('rating')
            : 0;

        return [
            'average_rating' => round($averageRating, 1),
            'total_reviews' => $totalReviews,
            'rating_breakdown' => [
                5 => $reviews[5] ?? 0,
                4 => $reviews[4] ?? 0,
                3 => $reviews[3] ?? 0,
                2 => $reviews[2] ?? 0,
                1 => $reviews[1] ?? 0,
            ],
        ];
    }
}

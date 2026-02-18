<?php

namespace App\Modules\Review\Services;

use App\Modules\Review\Models\Review;
use App\Modules\Review\Repositories\ReviewRepository;
use App\Modules\Order\Models\Order;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;

class ReviewService
{
    public function __construct(
        private readonly ReviewRepository $reviewRepository
    ) {}

    /**
     * Get reviews for product
     */
    public function getProductReviews(int $productId, array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        return $this->reviewRepository->getByProduct($productId, $filters, $perPage);
    }

    /**
     * Get all reviews (admin)
     */
    public function getPaginated(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->reviewRepository->getPaginated($filters, $perPage);
    }

    /**
     * Get user reviews
     */
    public function getUserReviews(User $user, int $perPage = 10): LengthAwarePaginator
    {
        return $this->reviewRepository->getByUser($user, $perPage);
    }

    /**
     * Get review by ID
     */
    public function getById(int $id): ?Review
    {
        return $this->reviewRepository->getById($id);
    }

    /**
     * Get product rating summary
     */
    public function getProductRatingSummary(int $productId): array
    {
        return $this->reviewRepository->getProductRatingSummary($productId);
    }

    /**
     * Create review
     */
    public function create(User $user, array $data): array
    {
        // Check if already reviewed
        if ($this->reviewRepository->hasUserReviewed($user->id, $data['product_id'])) {
            throw new \Exception('You have already reviewed this product');
        }

        // Check for verified purchase
        $isVerifiedPurchase = $this->checkVerifiedPurchase($user->id, $data['product_id']);

        $review = $this->reviewRepository->create([
            'user_id' => $user->id,
            'product_id' => $data['product_id'],
            'order_id' => $data['order_id'] ?? null,
            'rating' => $data['rating'],
            'title' => $data['title'] ?? null,
            'comment' => $data['comment'] ?? null,
            'is_verified_purchase' => $isVerifiedPurchase,
            'status' => Review::STATUS_PENDING, // Auto-approve can be configured
        ]);

        // Handle images
        if (!empty($data['images'])) {
            foreach ($data['images'] as $index => $image) {
                $path = $image->store('reviews', 'public');
                $this->reviewRepository->addImage($review, $path, $index);
            }
        }

        return [
            'review' => $review->load('images'),
            'message' => 'Review submitted for approval',
        ];
    }

    /**
     * Update review
     */
    public function update(Review $review, array $data): Review
    {
        // Reset to pending if content changed
        $contentChanged = false;
        if (isset($data['rating']) && $data['rating'] != $review->rating) {
            $contentChanged = true;
        }
        if (isset($data['comment']) && $data['comment'] != $review->comment) {
            $contentChanged = true;
        }

        if ($contentChanged && $review->isApproved()) {
            $data['status'] = Review::STATUS_PENDING;
        }

        return $this->reviewRepository->update($review, $data);
    }

    /**
     * Delete review
     */
    public function delete(Review $review): bool
    {
        // Delete images from storage
        foreach ($review->images as $image) {
            Storage::disk('public')->delete($image->image);
        }

        return $this->reviewRepository->delete($review);
    }

    /**
     * Approve review (admin)
     */
    public function approve(Review $review): Review
    {
        return $this->reviewRepository->update($review, [
            'status' => Review::STATUS_APPROVED,
        ]);
    }

    /**
     * Reject review (admin)
     */
    public function reject(Review $review): Review
    {
        return $this->reviewRepository->update($review, [
            'status' => Review::STATUS_REJECTED,
        ]);
    }

    /**
     * Add admin response
     */
    public function addAdminResponse(Review $review, string $response): Review
    {
        return $this->reviewRepository->update($review, [
            'admin_response' => $response,
            'admin_response_at' => now(),
        ]);
    }

    /**
     * Vote on review
     */
    public function vote(Review $review, User $user, bool $isHelpful): array
    {
        $vote = $this->reviewRepository->vote($review, $user->id, $isHelpful);

        return [
            'helpful_count' => $review->fresh()->helpful_count,
            'not_helpful_count' => $review->fresh()->not_helpful_count,
            'user_vote' => $vote->is_helpful,
        ];
    }

    /**
     * Check if user has purchased the product
     */
    protected function checkVerifiedPurchase(int $userId, int $productId): bool
    {
        return Order::where('user_id', $userId)
            ->where('status', Order::STATUS_DELIVERED)
            ->whereHas('items', function ($q) use ($productId) {
                $q->where('product_id', $productId);
            })
            ->exists();
    }

    /**
     * Check if user can review product
     */
    public function canUserReview(User $user, int $productId): array
    {
        $hasReviewed = $this->reviewRepository->hasUserReviewed($user->id, $productId);
        $hasPurchased = $this->checkVerifiedPurchase($user->id, $productId);

        return [
            'can_review' => !$hasReviewed,
            'has_reviewed' => $hasReviewed,
            'has_purchased' => $hasPurchased,
            'existing_review' => $hasReviewed 
                ? $this->reviewRepository->getUserReview($user->id, $productId)
                : null,
        ];
    }

    /**
     * Format review for response
     */
    public function formatReview(Review $review, ?int $currentUserId = null): array
    {
        return [
            'id' => $review->id,
            'rating' => $review->rating,
            'title' => $review->title,
            'comment' => $review->comment,
            'status' => $review->status,
            'is_verified_purchase' => $review->is_verified_purchase,
            'helpful_count' => $review->helpful_count,
            'not_helpful_count' => $review->not_helpful_count,
            'admin_response' => $review->admin_response,
            'admin_response_at' => $review->admin_response_at?->toISOString(),
            'user' => [
                'id' => $review->user->id,
                'name' => $review->user->name,
            ],
            'images' => $review->images->map(fn ($img) => [
                'id' => $img->id,
                'url' => Storage::url($img->image),
            ]),
            'user_vote' => $currentUserId && $review->hasUserVoted($currentUserId)
                ? $review->getUserVote($currentUserId)->is_helpful
                : null,
            'created_at' => $review->created_at->toISOString(),
            'updated_at' => $review->updated_at->toISOString(),
        ];
    }
}

<?php

namespace App\Modules\Review\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Review\Services\ReviewService;
use App\Modules\Review\Requests\StoreReviewRequest;
use App\Modules\Review\Requests\UpdateReviewRequest;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly ReviewService $reviewService
    ) {}

    /**
     * Get reviews for a product
     */
    public function productReviews(Request $request, int $productId): JsonResponse
    {
        $filters = [
            'rating' => $request->input('rating'),
            'verified_only' => $request->boolean('verified_only'),
            'with_images' => $request->boolean('with_images'),
            'sort_by' => $request->input('sort_by', 'created_at'),
        ];

        $perPage = $request->input('per_page', 10);
        $reviews = $this->reviewService->getProductReviews($productId, $filters, $perPage);

        $currentUserId = $request->user()?->id;

        return $this->paginatedResponse($reviews, function ($review) use ($currentUserId) {
            return $this->reviewService->formatReview($review, $currentUserId);
        });
    }

    /**
     * Get product rating summary
     */
    public function productRatingSummary(int $productId): JsonResponse
    {
        $summary = $this->reviewService->getProductRatingSummary($productId);
        return $this->successResponse($summary);
    }

    /**
     * Get user's reviews
     */
    public function myReviews(Request $request): JsonResponse
    {
        $perPage = $request->input('per_page', 10);
        $reviews = $this->reviewService->getUserReviews($request->user(), $perPage);

        return $this->paginatedResponse($reviews, function ($review) use ($request) {
            return $this->reviewService->formatReview($review, $request->user()->id);
        });
    }

    /**
     * Check if user can review product
     */
    public function canReview(Request $request, int $productId): JsonResponse
    {
        $result = $this->reviewService->canUserReview($request->user(), $productId);
        return $this->successResponse($result);
    }

    /**
     * Create review
     */
    public function store(StoreReviewRequest $request): JsonResponse
    {
        try {
            $result = $this->reviewService->create($request->user(), $request->validated());
            return $this->createdResponse(
                $this->reviewService->formatReview($result['review']),
                $result['message']
            );
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Update review
     */
    public function update(UpdateReviewRequest $request, int $id): JsonResponse
    {
        $review = $this->reviewService->getById($id);

        if (!$review) {
            return $this->notFoundResponse('Review not found');
        }

        // Check ownership
        if ($review->user_id !== $request->user()->id) {
            return $this->forbiddenResponse('You can only edit your own reviews');
        }

        $review = $this->reviewService->update($review, $request->validated());

        return $this->successResponse(
            $this->reviewService->formatReview($review),
            'Review updated'
        );
    }

    /**
     * Delete review
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $review = $this->reviewService->getById($id);

        if (!$review) {
            return $this->notFoundResponse('Review not found');
        }

        // Check ownership
        if ($review->user_id !== $request->user()->id) {
            return $this->forbiddenResponse('You can only delete your own reviews');
        }

        $this->reviewService->delete($review);

        return $this->successResponse(null, 'Review deleted');
    }

    /**
     * Vote on review
     */
    public function vote(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'is_helpful' => 'required|boolean',
        ]);

        $review = $this->reviewService->getById($id);

        if (!$review) {
            return $this->notFoundResponse('Review not found');
        }

        // Can't vote on own review
        if ($review->user_id === $request->user()->id) {
            return $this->errorResponse('You cannot vote on your own review');
        }

        $result = $this->reviewService->vote($review, $request->user(), $request->is_helpful);

        return $this->successResponse($result);
    }
}

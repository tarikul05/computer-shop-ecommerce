<?php

namespace App\Modules\Review\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\Review\Services\ReviewService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReviewAdminController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly ReviewService $reviewService
    ) {}

    /**
     * Get all reviews (paginated)
     */
    public function index(Request $request): JsonResponse
    {
        $filters = [
            'status' => $request->input('status'),
            'product_id' => $request->input('product_id'),
            'rating' => $request->input('rating'),
            'search' => $request->input('search'),
        ];

        $perPage = $request->input('per_page', 15);
        $reviews = $this->reviewService->getPaginated($filters, $perPage);

        return $this->paginatedResponse($reviews, function ($review) {
            return $this->reviewService->formatReview($review);
        });
    }

    /**
     * Get single review
     */
    public function show(int $id): JsonResponse
    {
        $review = $this->reviewService->getById($id);

        if (!$review) {
            return $this->notFoundResponse('Review not found');
        }

        return $this->successResponse($this->reviewService->formatReview($review));
    }

    /**
     * Approve review
     */
    public function approve(int $id): JsonResponse
    {
        $review = $this->reviewService->getById($id);

        if (!$review) {
            return $this->notFoundResponse('Review not found');
        }

        $review = $this->reviewService->approve($review);

        return $this->successResponse(
            $this->reviewService->formatReview($review),
            'Review approved'
        );
    }

    /**
     * Reject review
     */
    public function reject(int $id): JsonResponse
    {
        $review = $this->reviewService->getById($id);

        if (!$review) {
            return $this->notFoundResponse('Review not found');
        }

        $review = $this->reviewService->reject($review);

        return $this->successResponse(
            $this->reviewService->formatReview($review),
            'Review rejected'
        );
    }

    /**
     * Add admin response
     */
    public function respond(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'response' => 'required|string|max:1000',
        ]);

        $review = $this->reviewService->getById($id);

        if (!$review) {
            return $this->notFoundResponse('Review not found');
        }

        $review = $this->reviewService->addAdminResponse($review, $request->response);

        return $this->successResponse(
            $this->reviewService->formatReview($review),
            'Response added'
        );
    }

    /**
     * Delete review
     */
    public function destroy(int $id): JsonResponse
    {
        $review = $this->reviewService->getById($id);

        if (!$review) {
            return $this->notFoundResponse('Review not found');
        }

        $this->reviewService->delete($review);

        return $this->successResponse(null, 'Review deleted');
    }
}

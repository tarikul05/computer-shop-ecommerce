<?php

namespace App\Modules\Product\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Product\Services\CategoryService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly CategoryService $categoryService
    ) {}

    /**
     * Get all categories
     */
    public function index(): JsonResponse
    {
        $categories = $this->categoryService->getAll();

        return $this->successResponse($categories);
    }

    /**
     * Get category tree (for navigation)
     */
    public function tree(): JsonResponse
    {
        $tree = $this->categoryService->getCategoryTree();

        return $this->successResponse($tree);
    }

    /**
     * Get featured categories
     */
    public function featured(Request $request): JsonResponse
    {
        $limit = $request->input('limit', 10);
        $categories = $this->categoryService->getFeatured($limit);

        return $this->successResponse($categories);
    }

    /**
     * Get category by slug
     */
    public function show(string $slug): JsonResponse
    {
        $category = $this->categoryService->getBySlug($slug);

        if (!$category) {
            return $this->notFoundResponse('Category not found');
        }

        return $this->successResponse($category);
    }

    /**
     * Get categories for filter
     */
    public function forFilter(): JsonResponse
    {
        $categories = $this->categoryService->getForFilter();

        return $this->successResponse($categories);
    }
}

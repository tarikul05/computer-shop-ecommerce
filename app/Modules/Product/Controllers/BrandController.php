<?php

namespace App\Modules\Product\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Product\Services\BrandService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly BrandService $brandService
    ) {}

    /**
     * Get all brands
     */
    public function index(): JsonResponse
    {
        $brands = $this->brandService->getAll();

        return $this->successResponse($brands);
    }

    /**
     * Get featured brands
     */
    public function featured(Request $request): JsonResponse
    {
        $limit = $request->input('limit', 10);
        $brands = $this->brandService->getFeatured($limit);

        return $this->successResponse($brands);
    }

    /**
     * Get brand by slug
     */
    public function show(string $slug): JsonResponse
    {
        $brand = $this->brandService->getBySlug($slug);

        if (!$brand) {
            return $this->notFoundResponse('Brand not found');
        }

        return $this->successResponse($brand);
    }

    /**
     * Get brands for filter
     */
    public function forFilter(Request $request): JsonResponse
    {
        $categoryId = $request->input('category_id');
        $brands = $this->brandService->getForFilter($categoryId);

        return $this->successResponse($brands);
    }
}

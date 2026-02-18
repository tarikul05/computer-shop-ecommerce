<?php

namespace App\Modules\Product\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\Product\Services\BrandService;
use App\Modules\Product\Requests\StoreBrandRequest;
use App\Modules\Product\Requests\UpdateBrandRequest;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BrandAdminController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly BrandService $brandService
    ) {}

    /**
     * Get paginated brands
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->input('per_page', 15);
        $brands = $this->brandService->getPaginated($perPage);

        return $this->paginatedResponse($brands);
    }

    /**
     * Get all brands (for dropdown)
     */
    public function all(): JsonResponse
    {
        $brands = $this->brandService->getAll();

        return $this->successResponse($brands);
    }

    /**
     * Get brand by ID
     */
    public function show(int $id): JsonResponse
    {
        $brand = $this->brandService->getById($id);

        if (!$brand) {
            return $this->notFoundResponse('Brand not found');
        }

        return $this->successResponse($brand);
    }

    /**
     * Create brand
     */
    public function store(StoreBrandRequest $request): JsonResponse
    {
        $brand = $this->brandService->create($request->validated());

        return $this->createdResponse($brand, 'Brand created successfully');
    }

    /**
     * Update brand
     */
    public function update(UpdateBrandRequest $request, int $id): JsonResponse
    {
        $brand = $this->brandService->getById($id);

        if (!$brand) {
            return $this->notFoundResponse('Brand not found');
        }

        $brand = $this->brandService->update($brand, $request->validated());

        return $this->successResponse($brand, 'Brand updated successfully');
    }

    /**
     * Delete brand
     */
    public function destroy(int $id): JsonResponse
    {
        $brand = $this->brandService->getById($id);

        if (!$brand) {
            return $this->notFoundResponse('Brand not found');
        }

        $result = $this->brandService->delete($brand);

        if (!$result) {
            return $this->errorResponse('Cannot delete brand with products', 400);
        }

        return $this->successResponse(null, 'Brand deleted successfully');
    }
}

<?php

namespace App\Modules\Product\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\Product\Services\CategoryService;
use App\Modules\Product\Requests\StoreCategoryRequest;
use App\Modules\Product\Requests\UpdateCategoryRequest;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryAdminController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly CategoryService $categoryService
    ) {}

    /**
     * Get paginated categories
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->input('per_page', 15);
        $categories = $this->categoryService->getPaginated($perPage);

        return $this->paginatedResponse($categories);
    }

    /**
     * Get category tree for dropdown
     */
    public function tree(): JsonResponse
    {
        $tree = $this->categoryService->getTreeForDropdown();

        return $this->successResponse($tree);
    }

    /**
     * Get category by ID
     */
    public function show(int $id): JsonResponse
    {
        $category = $this->categoryService->getById($id);

        if (!$category) {
            return $this->notFoundResponse('Category not found');
        }

        return $this->successResponse($category);
    }

    /**
     * Create category
     */
    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $category = $this->categoryService->create($request->validated());

        return $this->createdResponse($category, 'Category created successfully');
    }

    /**
     * Update category
     */
    public function update(UpdateCategoryRequest $request, int $id): JsonResponse
    {
        $category = $this->categoryService->getById($id);

        if (!$category) {
            return $this->notFoundResponse('Category not found');
        }

        $category = $this->categoryService->update($category, $request->validated());

        return $this->successResponse($category, 'Category updated successfully');
    }

    /**
     * Delete category
     */
    public function destroy(int $id): JsonResponse
    {
        $category = $this->categoryService->getById($id);

        if (!$category) {
            return $this->notFoundResponse('Category not found');
        }

        $result = $this->categoryService->delete($category);

        if (!$result) {
            return $this->errorResponse('Cannot delete category with products', 400);
        }

        return $this->successResponse(null, 'Category deleted successfully');
    }
}

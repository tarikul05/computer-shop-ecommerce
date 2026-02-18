<?php

namespace App\Modules\Product\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\Product\Services\ProductService;
use App\Modules\Product\Requests\StoreProductRequest;
use App\Modules\Product\Requests\UpdateProductRequest;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductAdminController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly ProductService $productService
    ) {}

    /**
     * Get paginated products
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->only([
            'category_id',
            'brand_id',
            'search',
            'is_active',
            'is_featured',
            'sort',
        ]);

        $perPage = $request->input('per_page', 15);
        $products = $this->productService->getProductsAdmin($filters, $perPage);

        return $this->paginatedResponse($products);
    }

    /**
     * Get product by ID
     */
    public function show(int $id): JsonResponse
    {
        $product = $this->productService->getById($id);

        if (!$product) {
            return $this->notFoundResponse('Product not found');
        }

        return $this->successResponse($product);
    }

    /**
     * Create product
     */
    public function store(StoreProductRequest $request): JsonResponse
    {
        $product = $this->productService->create($request->validated());

        return $this->createdResponse($product, 'Product created successfully');
    }

    /**
     * Update product
     */
    public function update(UpdateProductRequest $request, int $id): JsonResponse
    {
        $product = $this->productService->getById($id);

        if (!$product) {
            return $this->notFoundResponse('Product not found');
        }

        $product = $this->productService->update($product, $request->validated());

        return $this->successResponse($product, 'Product updated successfully');
    }

    /**
     * Delete product
     */
    public function destroy(int $id): JsonResponse
    {
        $product = $this->productService->getById($id);

        if (!$product) {
            return $this->notFoundResponse('Product not found');
        }

        $this->productService->delete($product);

        return $this->successResponse(null, 'Product deleted successfully');
    }

    /**
     * Get low stock products
     */
    public function lowStock(): JsonResponse
    {
        $products = $this->productService->getLowStock();

        return $this->successResponse($products);
    }

    /**
     * Get out of stock products
     */
    public function outOfStock(): JsonResponse
    {
        $products = $this->productService->getOutOfStock();

        return $this->successResponse($products);
    }
}

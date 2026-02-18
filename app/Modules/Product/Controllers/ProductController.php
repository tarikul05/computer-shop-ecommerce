<?php

namespace App\Modules\Product\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Product\Services\ProductService;
use App\Modules\Product\Services\CategoryService;
use App\Modules\Product\Services\BrandService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly ProductService $productService,
        private readonly CategoryService $categoryService,
        private readonly BrandService $brandService
    ) {}

    /**
     * Get paginated products with filters
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->only([
            'category_id',
            'brand_id',
            'brand_ids',
            'min_price',
            'max_price',
            'search',
            'in_stock',
            'is_featured',
            'is_new',
            'sort',
        ]);

        $perPage = $request->input('per_page', 15);
        $products = $this->productService->getProducts($filters, $perPage);

        // Transform products for response
        $data = $products->getCollection()->map(
            fn($product) => $this->productService->formatProductList($product)
        );

        return $this->successResponse($data, 'Products retrieved successfully', 200, [
            'pagination' => [
                'total' => $products->total(),
                'per_page' => $products->perPage(),
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'from' => $products->firstItem(),
                'to' => $products->lastItem(),
            ],
        ]);
    }

    /**
     * Get product by slug
     */
    public function show(string $slug): JsonResponse
    {
        $product = $this->productService->getBySlug($slug);

        if (!$product) {
            return $this->notFoundResponse('Product not found');
        }

        return $this->successResponse($product);
    }

    /**
     * Get featured products
     */
    public function featured(Request $request): JsonResponse
    {
        $limit = $request->input('limit', 8);
        $products = $this->productService->getFeatured($limit);

        $data = $products->map(
            fn($product) => $this->productService->formatProductList($product)
        );

        return $this->successResponse($data);
    }

    /**
     * Get new arrivals
     */
    public function newArrivals(Request $request): JsonResponse
    {
        $limit = $request->input('limit', 8);
        $products = $this->productService->getNewArrivals($limit);

        $data = $products->map(
            fn($product) => $this->productService->formatProductList($product)
        );

        return $this->successResponse($data);
    }

    /**
     * Get best sellers
     */
    public function bestSellers(Request $request): JsonResponse
    {
        $limit = $request->input('limit', 8);
        $products = $this->productService->getBestSellers($limit);

        $data = $products->map(
            fn($product) => $this->productService->formatProductList($product)
        );

        return $this->successResponse($data);
    }

    /**
     * Get on sale products
     */
    public function onSale(Request $request): JsonResponse
    {
        $limit = $request->input('limit', 8);
        $products = $this->productService->getOnSale($limit);

        $data = $products->map(
            fn($product) => $this->productService->formatProductList($product)
        );

        return $this->successResponse($data);
    }

    /**
     * Get related products
     */
    public function related(string $slug, Request $request): JsonResponse
    {
        $productData = $this->productService->getBySlug($slug, false);

        if (!$productData) {
            return $this->notFoundResponse('Product not found');
        }

        $product = $this->productService->getById($productData['id']);
        $limit = $request->input('limit', 4);
        $related = $this->productService->getRelated($product, $limit);

        $data = $related->map(
            fn($product) => $this->productService->formatProductList($product)
        );

        return $this->successResponse($data);
    }

    /**
     * Search products
     */
    public function search(Request $request): JsonResponse
    {
        $term = $request->input('q', '');
        $limit = $request->input('limit', 10);

        if (strlen($term) < 2) {
            return $this->successResponse([]);
        }

        $products = $this->productService->search($term, $limit);

        $data = $products->map(
            fn($product) => $this->productService->formatProductList($product)
        );

        return $this->successResponse($data);
    }

    /**
     * Get filter options for product listing
     */
    public function filterOptions(Request $request): JsonResponse
    {
        $categoryId = $request->input('category_id');

        return $this->successResponse([
            'categories' => $this->categoryService->getForFilter(),
            'brands' => $this->brandService->getForFilter($categoryId),
            'price_range' => $this->productService->getPriceRange($categoryId),
        ]);
    }
}

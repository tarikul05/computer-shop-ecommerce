<?php

namespace App\Modules\Product\Services;

use App\Modules\Product\Models\Product;
use App\Modules\Product\Models\ProductImage;
use App\Modules\Product\Models\ProductSpecification;
use App\Modules\Product\Repositories\ProductRepository;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductService
{
    public function __construct(
        private readonly ProductRepository $productRepository
    ) {}

    /**
     * Get paginated products with filters
     */
    public function getProducts(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->productRepository->paginate($filters, $perPage);
    }

    /**
     * Get paginated products for admin
     */
    public function getProductsAdmin(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->productRepository->paginateAdmin($filters, $perPage);
    }

    /**
     * Get featured products
     */
    public function getFeatured(int $limit = 8): Collection
    {
        return $this->productRepository->getFeatured($limit);
    }

    /**
     * Get new arrivals
     */
    public function getNewArrivals(int $limit = 8): Collection
    {
        return $this->productRepository->getNewArrivals($limit);
    }

    /**
     * Get best sellers
     */
    public function getBestSellers(int $limit = 8): Collection
    {
        return $this->productRepository->getBestSellers($limit);
    }

    /**
     * Get on sale products
     */
    public function getOnSale(int $limit = 8): Collection
    {
        return $this->productRepository->getOnSale($limit);
    }

    /**
     * Get product by slug
     */
    public function getBySlug(string $slug, bool $incrementViews = true): ?array
    {
        $product = $this->productRepository->findActiveBySlug($slug);

        if (!$product) {
            return null;
        }

        if ($incrementViews) {
            $this->productRepository->incrementViews($product);
        }

        return $this->formatProductDetail($product);
    }

    /**
     * Get product by ID
     */
    public function getById(int $id): ?Product
    {
        return $this->productRepository->findById($id);
    }

    /**
     * Get related products
     */
    public function getRelated(Product $product, int $limit = 4): Collection
    {
        return $this->productRepository->getRelated($product, $limit);
    }

    /**
     * Search products
     */
    public function search(string $term, int $limit = 10): Collection
    {
        return $this->productRepository->search($term, $limit);
    }

    /**
     * Create product with images and specifications
     */
    public function create(array $data): Product
    {
        return DB::transaction(function () use ($data) {
            // Extract nested data
            $images = $data['images'] ?? [];
            $specifications = $data['specifications'] ?? [];
            unset($data['images'], $data['specifications']);

            // Create product
            $product = $this->productRepository->create($data);

            // Add images
            $this->syncImages($product, $images);

            // Add specifications
            $this->syncSpecifications($product, $specifications);

            return $product->fresh(['images', 'specifications']);
        });
    }

    /**
     * Update product with images and specifications
     */
    public function update(Product $product, array $data): Product
    {
        return DB::transaction(function () use ($product, $data) {
            // Extract nested data
            $images = $data['images'] ?? null;
            $specifications = $data['specifications'] ?? null;
            unset($data['images'], $data['specifications']);

            // Update product
            $product = $this->productRepository->update($product, $data);

            // Update images if provided
            if ($images !== null) {
                $this->syncImages($product, $images);
            }

            // Update specifications if provided
            if ($specifications !== null) {
                $this->syncSpecifications($product, $specifications);
            }

            return $product->fresh(['images', 'specifications']);
        });
    }

    /**
     * Delete product
     */
    public function delete(Product $product): bool
    {
        return DB::transaction(function () use ($product) {
            // Delete images from storage
            foreach ($product->images as $image) {
                if ($image->image && !filter_var($image->image, FILTER_VALIDATE_URL)) {
                    Storage::delete($image->image);
                }
            }

            return $this->productRepository->delete($product);
        });
    }

    /**
     * Get price range for filters
     */
    public function getPriceRange(?int $categoryId = null): array
    {
        return $this->productRepository->getPriceRange($categoryId);
    }

    /**
     * Get low stock products
     */
    public function getLowStock(): Collection
    {
        return $this->productRepository->getLowStock();
    }

    /**
     * Get out of stock products
     */
    public function getOutOfStock(): Collection
    {
        return $this->productRepository->getOutOfStock();
    }

    /**
     * Sync product images
     */
    protected function syncImages(Product $product, array $images): void
    {
        // Delete existing images
        $product->images()->delete();

        foreach ($images as $index => $imageData) {
            ProductImage::create([
                'product_id' => $product->id,
                'image' => $imageData['image'],
                'alt_text' => $imageData['alt_text'] ?? null,
                'is_primary' => $index === 0 || ($imageData['is_primary'] ?? false),
                'sort_order' => $imageData['sort_order'] ?? $index,
            ]);
        }
    }

    /**
     * Sync product specifications
     */
    protected function syncSpecifications(Product $product, array $specifications): void
    {
        // Delete existing specifications
        $product->specifications()->delete();

        foreach ($specifications as $index => $specData) {
            ProductSpecification::create([
                'product_id' => $product->id,
                'specification_group_id' => $specData['group_id'] ?? null,
                'name' => $specData['name'],
                'value' => $specData['value'],
                'sort_order' => $specData['sort_order'] ?? $index,
            ]);
        }
    }

    /**
     * Format product for listing
     */
    public function formatProductList(Product $product): array
    {
        return [
            'id' => $product->id,
            'name' => $product->name,
            'slug' => $product->slug,
            'short_description' => $product->short_description,
            'price' => $product->price,
            'compare_price' => $product->compare_price,
            'is_on_sale' => $product->is_on_sale,
            'discount_percentage' => $product->discount_percentage,
            'is_in_stock' => $product->is_in_stock,
            'stock_status' => $product->stock_status,
            'is_new' => $product->is_new,
            'is_featured' => $product->is_featured,
            'average_rating' => $product->average_rating,
            'reviews_count' => $product->reviews_count,
            'image' => $product->primary_image_url,
            'category' => $product->category ? [
                'id' => $product->category->id,
                'name' => $product->category->name,
                'slug' => $product->category->slug,
            ] : null,
            'brand' => $product->brand ? [
                'id' => $product->brand->id,
                'name' => $product->brand->name,
                'slug' => $product->brand->slug,
            ] : null,
        ];
    }

    /**
     * Format product for detail view
     */
    protected function formatProductDetail(Product $product): array
    {
        // Group specifications by group
        $specifications = $product->specifications->groupBy(function ($spec) {
            return $spec->group?->name ?? 'General';
        })->map(function ($specs) {
            return $specs->map(fn($spec) => [
                'name' => $spec->name,
                'value' => $spec->value,
            ])->values();
        });

        return [
            'id' => $product->id,
            'name' => $product->name,
            'slug' => $product->slug,
            'sku' => $product->sku,
            'short_description' => $product->short_description,
            'description' => $product->description,
            'price' => $product->price,
            'compare_price' => $product->compare_price,
            'is_on_sale' => $product->is_on_sale,
            'discount_percentage' => $product->discount_percentage,
            'quantity' => $product->quantity,
            'is_in_stock' => $product->is_in_stock,
            'stock_status' => $product->stock_status,
            'is_new' => $product->is_new,
            'is_featured' => $product->is_featured,
            'warranty_info' => $product->warranty_info,
            'warranty_period' => $product->warranty_period,
            'average_rating' => $product->average_rating,
            'reviews_count' => $product->reviews_count,
            'views_count' => $product->views_count,
            'images' => $product->images->map(fn($img) => [
                'id' => $img->id,
                'url' => $img->image_url,
                'alt' => $img->alt_text,
                'is_primary' => $img->is_primary,
            ]),
            'specifications' => $specifications,
            'category' => $product->category ? [
                'id' => $product->category->id,
                'name' => $product->category->name,
                'slug' => $product->category->slug,
                'breadcrumb' => $this->getCategoryBreadcrumb($product->category),
            ] : null,
            'brand' => $product->brand ? [
                'id' => $product->brand->id,
                'name' => $product->brand->name,
                'slug' => $product->brand->slug,
                'logo' => $product->brand->logo,
            ] : null,
            'meta_title' => $product->meta_title ?? $product->name,
            'meta_description' => $product->meta_description ?? $product->short_description,
        ];
    }

    /**
     * Get category breadcrumb
     */
    protected function getCategoryBreadcrumb($category): array
    {
        $breadcrumb = [];
        $current = $category;

        while ($current) {
            array_unshift($breadcrumb, [
                'name' => $current->name,
                'slug' => $current->slug,
            ]);
            $current = $current->parent;
        }

        return $breadcrumb;
    }
}

<?php

namespace App\Modules\Product\Repositories;

use App\Modules\Product\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class ProductRepository
{
    /**
     * Base query with common relations
     */
    protected function baseQuery(): Builder
    {
        return Product::with(['category', 'brand', 'primaryImage']);
    }

    /**
     * Get all active products
     */
    public function all(): Collection
    {
        return $this->baseQuery()->active()->get();
    }

    /**
     * Get paginated products with filters
     */
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->baseQuery()->active();
        
        $query = $this->applyFilters($query, $filters);
        $query = $this->applySorting($query, $filters['sort'] ?? null);

        return $query->paginate($perPage);
    }

    /**
     * Get paginated products for admin
     */
    public function paginateAdmin(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->baseQuery();
        
        $query = $this->applyFilters($query, $filters);
        $query = $this->applySorting($query, $filters['sort'] ?? null);

        return $query->paginate($perPage);
    }

    /**
     * Get featured products
     */
    public function getFeatured(int $limit = 8): Collection
    {
        return $this->baseQuery()
            ->active()
            ->featured()
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Get new arrivals
     */
    public function getNewArrivals(int $limit = 8): Collection
    {
        return $this->baseQuery()
            ->active()
            ->new()
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Get best sellers
     */
    public function getBestSellers(int $limit = 8): Collection
    {
        return $this->baseQuery()
            ->active()
            ->bestSeller()
            ->orderBy('sales_count', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get on sale products
     */
    public function getOnSale(int $limit = 8): Collection
    {
        return $this->baseQuery()
            ->active()
            ->whereNotNull('compare_price')
            ->whereColumn('compare_price', '>', 'price')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get related products
     */
    public function getRelated(Product $product, int $limit = 4): Collection
    {
        return $this->baseQuery()
            ->active()
            ->where('id', '!=', $product->id)
            ->where(function ($query) use ($product) {
                $query->where('category_id', $product->category_id)
                      ->orWhere('brand_id', $product->brand_id);
            })
            ->inRandomOrder()
            ->limit($limit)
            ->get();
    }

    /**
     * Find by ID
     */
    public function findById(int $id): ?Product
    {
        return Product::with(['category', 'brand', 'images', 'specifications.group'])->find($id);
    }

    /**
     * Find by slug
     */
    public function findBySlug(string $slug): ?Product
    {
        return Product::with(['category', 'brand', 'images', 'specifications.group'])
            ->where('slug', $slug)
            ->first();
    }

    /**
     * Find active product by slug
     */
    public function findActiveBySlug(string $slug): ?Product
    {
        return Product::with(['category', 'brand', 'images', 'specifications.group'])
            ->active()
            ->where('slug', $slug)
            ->first();
    }

    /**
     * Search products
     */
    public function search(string $term, int $limit = 10): Collection
    {
        return $this->baseQuery()
            ->active()
            ->search($term)
            ->limit($limit)
            ->get();
    }

    /**
     * Create product
     */
    public function create(array $data): Product
    {
        return Product::create($data);
    }

    /**
     * Update product
     */
    public function update(Product $product, array $data): Product
    {
        $product->update($data);
        return $product->fresh();
    }

    /**
     * Delete product
     */
    public function delete(Product $product): bool
    {
        return $product->delete();
    }

    /**
     * Increment views count
     */
    public function incrementViews(Product $product): void
    {
        $product->increment('views_count');
    }

    /**
     * Decrement stock
     */
    public function decrementStock(Product $product, int $quantity): void
    {
        $product->decrement('quantity', $quantity);
    }

    /**
     * Increment stock
     */
    public function incrementStock(Product $product, int $quantity): void
    {
        $product->increment('quantity', $quantity);
    }

    /**
     * Get low stock products
     */
    public function getLowStock(): Collection
    {
        return Product::active()
            ->lowStock()
            ->with(['category', 'brand'])
            ->get();
    }

    /**
     * Get out of stock products
     */
    public function getOutOfStock(): Collection
    {
        return Product::active()
            ->outOfStock()
            ->with(['category', 'brand'])
            ->get();
    }

    /**
     * Apply filters to query
     */
    protected function applyFilters(Builder $query, array $filters): Builder
    {
        if (!empty($filters['category_id'])) {
            $query->inCategory($filters['category_id']);
        }

        if (!empty($filters['brand_id'])) {
            $query->ofBrand($filters['brand_id']);
        }

        if (!empty($filters['brand_ids']) && is_array($filters['brand_ids'])) {
            $query->whereIn('brand_id', $filters['brand_ids']);
        }

        if (isset($filters['min_price']) && isset($filters['max_price'])) {
            $query->priceBetween($filters['min_price'], $filters['max_price']);
        } elseif (isset($filters['min_price'])) {
            $query->where('price', '>=', $filters['min_price']);
        } elseif (isset($filters['max_price'])) {
            $query->where('price', '<=', $filters['max_price']);
        }

        if (!empty($filters['search'])) {
            $query->search($filters['search']);
        }

        if (isset($filters['in_stock']) && $filters['in_stock']) {
            $query->inStock();
        }

        if (isset($filters['is_featured'])) {
            $query->where('is_featured', $filters['is_featured']);
        }

        if (isset($filters['is_new'])) {
            $query->where('is_new', $filters['is_new']);
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        return $query;
    }

    /**
     * Apply sorting to query
     */
    protected function applySorting(Builder $query, ?string $sort): Builder
    {
        return match ($sort) {
            'price_low' => $query->orderBy('price', 'asc'),
            'price_high' => $query->orderBy('price', 'desc'),
            'name_asc' => $query->orderBy('name', 'asc'),
            'name_desc' => $query->orderBy('name', 'desc'),
            'newest' => $query->orderBy('created_at', 'desc'),
            'oldest' => $query->orderBy('created_at', 'asc'),
            'popular' => $query->orderBy('views_count', 'desc'),
            'best_selling' => $query->orderBy('sales_count', 'desc'),
            default => $query->orderBy('created_at', 'desc'),
        };
    }

    /**
     * Get price range for category
     */
    public function getPriceRange(?int $categoryId = null): array
    {
        $query = Product::active();
        
        if ($categoryId) {
            $query->inCategory($categoryId);
        }

        return [
            'min' => (float) $query->min('price') ?? 0,
            'max' => (float) $query->max('price') ?? 0,
        ];
    }
}

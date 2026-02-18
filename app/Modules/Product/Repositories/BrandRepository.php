<?php

namespace App\Modules\Product\Repositories;

use App\Modules\Product\Models\Brand;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class BrandRepository
{
    /**
     * Get all brands
     */
    public function all(): Collection
    {
        return Brand::active()->ordered()->get();
    }

    /**
     * Get paginated brands
     */
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return Brand::ordered()->paginate($perPage);
    }

    /**
     * Get featured brands
     */
    public function getFeatured(int $limit = 10): Collection
    {
        return Brand::active()
            ->featured()
            ->ordered()
            ->limit($limit)
            ->get();
    }

    /**
     * Find by ID
     */
    public function findById(int $id): ?Brand
    {
        return Brand::find($id);
    }

    /**
     * Find by slug
     */
    public function findBySlug(string $slug): ?Brand
    {
        return Brand::where('slug', $slug)->first();
    }

    /**
     * Find by slug with products
     */
    public function findBySlugWithProducts(string $slug): ?Brand
    {
        return Brand::where('slug', $slug)
            ->with(['products' => function ($query) {
                $query->active()->with(['primaryImage', 'category']);
            }])
            ->first();
    }

    /**
     * Create brand
     */
    public function create(array $data): Brand
    {
        return Brand::create($data);
    }

    /**
     * Update brand
     */
    public function update(Brand $brand, array $data): Brand
    {
        $brand->update($data);
        return $brand->fresh();
    }

    /**
     * Delete brand
     */
    public function delete(Brand $brand): bool
    {
        return $brand->delete();
    }

    /**
     * Get brands for filter
     */
    public function getForFilter(?int $categoryId = null): Collection
    {
        $query = Brand::active()
            ->withCount(['products' => function ($query) use ($categoryId) {
                $query->active();
                if ($categoryId) {
                    $query->where('category_id', $categoryId);
                }
            }])
            ->having('products_count', '>', 0)
            ->ordered();

        return $query->get();
    }
}

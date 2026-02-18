<?php

namespace App\Modules\Product\Repositories;

use App\Modules\Product\Models\Category;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class CategoryRepository
{
    /**
     * Get all categories
     */
    public function all(): Collection
    {
        return Category::active()->ordered()->get();
    }

    /**
     * Get paginated categories
     */
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return Category::ordered()->paginate($perPage);
    }

    /**
     * Get root categories with children
     */
    public function getRootWithChildren(): Collection
    {
        return Category::active()
            ->root()
            ->with(['children' => function ($query) {
                $query->active()->ordered()->with(['children' => function ($q) {
                    $q->active()->ordered();
                }]);
            }])
            ->ordered()
            ->get();
    }

    /**
     * Get featured categories
     */
    public function getFeatured(int $limit = 10): Collection
    {
        return Category::active()
            ->featured()
            ->ordered()
            ->limit($limit)
            ->get();
    }

    /**
     * Find by ID
     */
    public function findById(int $id): ?Category
    {
        return Category::find($id);
    }

    /**
     * Find by slug
     */
    public function findBySlug(string $slug): ?Category
    {
        return Category::where('slug', $slug)->first();
    }

    /**
     * Find by slug with products
     */
    public function findBySlugWithProducts(string $slug): ?Category
    {
        return Category::where('slug', $slug)
            ->with(['products' => function ($query) {
                $query->active()->with(['primaryImage', 'brand']);
            }])
            ->first();
    }

    /**
     * Create category
     */
    public function create(array $data): Category
    {
        return Category::create($data);
    }

    /**
     * Update category
     */
    public function update(Category $category, array $data): Category
    {
        $category->update($data);
        return $category->fresh();
    }

    /**
     * Delete category
     */
    public function delete(Category $category): bool
    {
        return $category->delete();
    }

    /**
     * Get category tree (for admin dropdown)
     */
    public function getTree(): Collection
    {
        return Category::root()
            ->with('descendants')
            ->ordered()
            ->get();
    }

    /**
     * Get categories for filter
     */
    public function getForFilter(): Collection
    {
        return Category::active()
            ->withCount(['products' => function ($query) {
                $query->active();
            }])
            ->having('products_count', '>', 0)
            ->ordered()
            ->get();
    }
}

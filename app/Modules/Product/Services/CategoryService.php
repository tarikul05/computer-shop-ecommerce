<?php

namespace App\Modules\Product\Services;

use App\Modules\Product\Models\Category;
use App\Modules\Product\Repositories\CategoryRepository;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class CategoryService
{
    public function __construct(
        private readonly CategoryRepository $categoryRepository
    ) {}

    /**
     * Get all active categories
     */
    public function getAll(): Collection
    {
        return $this->categoryRepository->all();
    }

    /**
     * Get paginated categories (admin)
     */
    public function getPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return $this->categoryRepository->paginate($perPage);
    }

    /**
     * Get category tree for navigation
     */
    public function getCategoryTree(): Collection
    {
        return $this->categoryRepository->getRootWithChildren();
    }

    /**
     * Get featured categories
     */
    public function getFeatured(int $limit = 10): Collection
    {
        return $this->categoryRepository->getFeatured($limit);
    }

    /**
     * Get category by slug
     */
    public function getBySlug(string $slug): ?array
    {
        $category = $this->categoryRepository->findBySlug($slug);

        if (!$category || !$category->is_active) {
            return null;
        }

        return $this->formatCategory($category);
    }

    /**
     * Get category by ID
     */
    public function getById(int $id): ?Category
    {
        return $this->categoryRepository->findById($id);
    }

    /**
     * Create category
     */
    public function create(array $data): Category
    {
        return $this->categoryRepository->create($data);
    }

    /**
     * Update category
     */
    public function update(Category $category, array $data): Category
    {
        return $this->categoryRepository->update($category, $data);
    }

    /**
     * Delete category
     */
    public function delete(Category $category): bool
    {
        // Check if category has products
        if ($category->products()->exists()) {
            return false;
        }

        // Move children to parent or root
        $category->children()->update(['parent_id' => $category->parent_id]);

        return $this->categoryRepository->delete($category);
    }

    /**
     * Get categories for filter
     */
    public function getForFilter(): Collection
    {
        return $this->categoryRepository->getForFilter();
    }

    /**
     * Get category tree for dropdown
     */
    public function getTreeForDropdown(): array
    {
        $categories = $this->categoryRepository->getTree();
        return $this->flattenTree($categories);
    }

    /**
     * Format category data
     */
    protected function formatCategory(Category $category): array
    {
        return [
            'id' => $category->id,
            'name' => $category->name,
            'slug' => $category->slug,
            'description' => $category->description,
            'image' => $category->image,
            'icon' => $category->icon,
            'parent' => $category->parent ? [
                'id' => $category->parent->id,
                'name' => $category->parent->name,
                'slug' => $category->parent->slug,
            ] : null,
            'children' => $category->children->map(fn($child) => [
                'id' => $child->id,
                'name' => $child->name,
                'slug' => $child->slug,
            ]),
            'breadcrumb' => $this->getBreadcrumb($category),
            'meta_title' => $category->meta_title ?? $category->name,
            'meta_description' => $category->meta_description,
        ];
    }

    /**
     * Get breadcrumb for category
     */
    protected function getBreadcrumb(Category $category): array
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

    /**
     * Flatten category tree for dropdown
     */
    protected function flattenTree(Collection $categories, int $depth = 0): array
    {
        $result = [];

        foreach ($categories as $category) {
            $prefix = str_repeat('— ', $depth);
            $result[] = [
                'id' => $category->id,
                'name' => $prefix . $category->name,
                'depth' => $depth,
            ];

            if ($category->children->isNotEmpty()) {
                $result = array_merge(
                    $result,
                    $this->flattenTree($category->children, $depth + 1)
                );
            }
        }

        return $result;
    }
}

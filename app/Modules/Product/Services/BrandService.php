<?php

namespace App\Modules\Product\Services;

use App\Modules\Product\Models\Brand;
use App\Modules\Product\Repositories\BrandRepository;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class BrandService
{
    public function __construct(
        private readonly BrandRepository $brandRepository
    ) {}

    /**
     * Get all active brands
     */
    public function getAll(): Collection
    {
        return $this->brandRepository->all();
    }

    /**
     * Get paginated brands (admin)
     */
    public function getPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return $this->brandRepository->paginate($perPage);
    }

    /**
     * Get featured brands
     */
    public function getFeatured(int $limit = 10): Collection
    {
        return $this->brandRepository->getFeatured($limit);
    }

    /**
     * Get brand by slug
     */
    public function getBySlug(string $slug): ?array
    {
        $brand = $this->brandRepository->findBySlug($slug);

        if (!$brand || !$brand->is_active) {
            return null;
        }

        return $this->formatBrand($brand);
    }

    /**
     * Get brand by ID
     */
    public function getById(int $id): ?Brand
    {
        return $this->brandRepository->findById($id);
    }

    /**
     * Create brand
     */
    public function create(array $data): Brand
    {
        return $this->brandRepository->create($data);
    }

    /**
     * Update brand
     */
    public function update(Brand $brand, array $data): Brand
    {
        return $this->brandRepository->update($brand, $data);
    }

    /**
     * Delete brand
     */
    public function delete(Brand $brand): bool
    {
        // Check if brand has products
        if ($brand->products()->exists()) {
            return false;
        }

        return $this->brandRepository->delete($brand);
    }

    /**
     * Get brands for filter
     */
    public function getForFilter(?int $categoryId = null): Collection
    {
        return $this->brandRepository->getForFilter($categoryId);
    }

    /**
     * Format brand data
     */
    protected function formatBrand(Brand $brand): array
    {
        return [
            'id' => $brand->id,
            'name' => $brand->name,
            'slug' => $brand->slug,
            'description' => $brand->description,
            'logo' => $brand->logo,
            'website' => $brand->website,
            'product_count' => $brand->products()->active()->count(),
            'meta_title' => $brand->meta_title ?? $brand->name,
            'meta_description' => $brand->meta_description,
        ];
    }
}

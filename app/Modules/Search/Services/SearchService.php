<?php

namespace App\Modules\Search\Services;

use App\Modules\Search\Repositories\SearchRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class SearchService
{
    protected SearchRepositoryInterface $repository;
    
    /**
     * Cache duration in seconds (5 minutes for search results)
     */
    protected int $cacheDuration = 300;

    public function __construct(SearchRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Perform product search with caching
     */
    public function searchProducts(string $query, array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $query = $this->sanitizeQuery($query);
        $cacheKey = $this->generateCacheKey('search', $query, $filters, $perPage);

        // Get results (cached for repeated searches)
        $results = Cache::remember($cacheKey, $this->cacheDuration, function () use ($query, $filters, $perPage) {
            return $this->repository->searchProducts($query, $filters, $perPage);
        });

        // Track search asynchronously (don't block response)
        if (!empty($query)) {
            $this->trackSearch($query, $filters, $results->total());
        }

        return $results;
    }

    /**
     * Search all entities (products, categories, brands)
     */
    public function searchAll(string $query, int $limit = 10): array
    {
        $query = $this->sanitizeQuery($query);
        
        if (empty($query)) {
            return [
                'products' => [],
                'categories' => [],
                'brands' => [],
            ];
        }

        $cacheKey = $this->generateCacheKey('search_all', $query, [], $limit);

        return Cache::remember($cacheKey, $this->cacheDuration, function () use ($query, $limit) {
            return $this->repository->searchAll($query, $limit);
        });
    }

    /**
     * Get search suggestions for autocomplete
     */
    public function getSuggestions(string $query, int $limit = 10): Collection
    {
        $query = $this->sanitizeQuery($query);
        
        if (strlen($query) < 2) {
            return collect();
        }

        $cacheKey = $this->generateCacheKey('suggestions', $query, [], $limit);

        return Cache::remember($cacheKey, 60, function () use ($query, $limit) {
            return $this->repository->getSuggestions($query, $limit);
        });
    }

    /**
     * Get autocomplete results
     */
    public function getAutocomplete(string $query, int $limit = 8): array
    {
        $query = $this->sanitizeQuery($query);
        
        if (strlen($query) < 2) {
            return [
                'suggestions' => [],
                'products' => [],
                'categories' => [],
                'brands' => [],
            ];
        }

        $cacheKey = $this->generateCacheKey('autocomplete', $query, [], $limit);

        return Cache::remember($cacheKey, 60, function () use ($query, $limit) {
            return $this->repository->getAutocomplete($query, $limit);
        });
    }

    /**
     * Get user's search history
     */
    public function getUserSearchHistory(int $limit = 10): Collection
    {
        $userId = Auth::id();
        $sessionId = session()->getId();

        return $this->repository->getUserSearchHistory($userId, $sessionId, $limit);
    }

    /**
     * Clear user's search history
     */
    public function clearUserSearchHistory(): void
    {
        $userId = Auth::id();
        $sessionId = session()->getId();

        $this->repository->clearUserSearchHistory($userId, $sessionId);
    }

    /**
     * Get popular searches
     */
    public function getPopularSearches(int $limit = 10): Collection
    {
        return Cache::remember('popular_searches_' . $limit, 300, function () use ($limit) {
            return $this->repository->getPopularSearches($limit);
        });
    }

    /**
     * Get trending searches
     */
    public function getTrendingSearches(int $days = 7, int $limit = 10): Collection
    {
        return Cache::remember("trending_searches_{$days}_{$limit}", 300, function () use ($days, $limit) {
            return $this->repository->getTrendingSearches($days, $limit);
        });
    }

    /**
     * Track click on search result
     */
    public function trackClick(string $query): void
    {
        $query = $this->sanitizeQuery($query);
        
        if (!empty($query)) {
            $this->repository->trackClick($query);
        }
    }

    /**
     * Track conversion from search (add to cart or purchase)
     */
    public function trackConversion(string $query): void
    {
        $query = $this->sanitizeQuery($query);
        
        if (!empty($query)) {
            $this->repository->trackConversion($query);
        }
    }

    /**
     * Get search filters/facets based on current results
     */
    public function getSearchFilters(string $query, array $currentFilters = []): array
    {
        // This would return available filter options based on search results
        // For now, return static filter options
        return Cache::remember('search_filters', 3600, function () {
            return [
                'price_ranges' => [
                    ['min' => 0, 'max' => 5000, 'label' => 'Under ৳5,000'],
                    ['min' => 5000, 'max' => 10000, 'label' => '৳5,000 - ৳10,000'],
                    ['min' => 10000, 'max' => 25000, 'label' => '৳10,000 - ৳25,000'],
                    ['min' => 25000, 'max' => 50000, 'label' => '৳25,000 - ৳50,000'],
                    ['min' => 50000, 'max' => 100000, 'label' => '৳50,000 - ৳100,000'],
                    ['min' => 100000, 'max' => null, 'label' => 'Over ৳100,000'],
                ],
                'rating_options' => [
                    ['value' => 4, 'label' => '4★ & above'],
                    ['value' => 3, 'label' => '3★ & above'],
                    ['value' => 2, 'label' => '2★ & above'],
                ],
                'sort_options' => [
                    ['value' => 'relevance', 'label' => 'Relevance'],
                    ['value' => 'price_asc', 'label' => 'Price: Low to High'],
                    ['value' => 'price_desc', 'label' => 'Price: High to Low'],
                    ['value' => 'newest', 'label' => 'Newest First'],
                    ['value' => 'rating', 'label' => 'Customer Rating'],
                    ['value' => 'popularity', 'label' => 'Popularity'],
                ],
            ];
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Private Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Track search query in history and popular searches
     */
    private function trackSearch(string $query, array $filters, int $resultsCount): void
    {
        $userId = Auth::id();
        $sessionId = session()->getId();

        // Save to search history
        $this->repository->saveSearchHistory(
            $query,
            $filters,
            $resultsCount,
            $userId,
            $sessionId
        );

        // Track as popular search
        $this->repository->trackPopularSearch($query);

        // Clear cached popular/trending searches to reflect new data
        Cache::forget('popular_searches_10');
        Cache::forget('trending_searches_7_10');
    }

    /**
     * Sanitize search query
     */
    private function sanitizeQuery(string $query): string
    {
        // Remove potentially harmful characters
        $query = strip_tags($query);
        
        // Normalize whitespace
        $query = preg_replace('/\s+/', ' ', $query);
        
        // Trim and limit length
        $query = Str::limit(trim($query), 200, '');
        
        return $query;
    }

    /**
     * Generate cache key for search results
     */
    private function generateCacheKey(string $prefix, string $query, array $filters, int $limit): string
    {
        $filterHash = md5(json_encode($filters));
        $queryHash = md5(strtolower($query));
        
        return "search:{$prefix}:{$queryHash}:{$filterHash}:{$limit}";
    }
}

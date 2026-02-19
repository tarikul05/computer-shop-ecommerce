<?php

namespace App\Modules\Search\Repositories;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface SearchRepositoryInterface
{
    /**
     * Search products with filters
     */
    public function searchProducts(string $query, array $filters = [], int $perPage = 20): LengthAwarePaginator;

    /**
     * Search across all entities (products, categories, brands)
     */
    public function searchAll(string $query, int $limit = 10): array;

    /**
     * Get search suggestions based on partial query
     */
    public function getSuggestions(string $query, int $limit = 10): Collection;

    /**
     * Get autocomplete results
     */
    public function getAutocomplete(string $query, int $limit = 8): array;

    /**
     * Save search to history
     */
    public function saveSearchHistory(string $query, array $filters, int $resultsCount, ?int $userId, ?string $sessionId): void;

    /**
     * Get user's search history
     */
    public function getUserSearchHistory(?int $userId, ?string $sessionId, int $limit = 10): Collection;

    /**
     * Clear user's search history
     */
    public function clearUserSearchHistory(?int $userId, ?string $sessionId): void;

    /**
     * Track popular search
     */
    public function trackPopularSearch(string $query): void;

    /**
     * Get popular searches
     */
    public function getPopularSearches(int $limit = 10): Collection;

    /**
     * Get trending searches
     */
    public function getTrendingSearches(int $days = 7, int $limit = 10): Collection;

    /**
     * Track click on search result
     */
    public function trackClick(string $query): void;

    /**
     * Track conversion from search
     */
    public function trackConversion(string $query): void;
}

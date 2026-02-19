<?php

namespace App\Modules\Search\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use App\Modules\Search\Services\SearchService;
use App\Modules\Search\Http\Requests\SearchRequest;
use App\Modules\Search\Http\Requests\AutocompleteRequest;
use App\Modules\Search\Http\Requests\TrackSearchRequest;
use App\Modules\Search\Http\Resources\SearchResultResource;
use App\Modules\Search\Http\Resources\SearchResultCollection;
use Illuminate\Http\JsonResponse;

class SearchController extends Controller
{
    use ApiResponse;

    protected SearchService $searchService;

    public function __construct(SearchService $searchService)
    {
        $this->searchService = $searchService;
    }

    /**
     * Search products with filters
     * 
     * @param SearchRequest $request
     * @return JsonResponse
     */
    public function search(SearchRequest $request): JsonResponse
    {
        $query = $request->getSearchQuery();
        $filters = $request->getFilters();
        $perPage = $request->getPerPage();

        $results = $this->searchService->searchProducts($query, $filters, $perPage);

        return $this->successResponse(
            new SearchResultCollection($results),
            'Search completed successfully'
        );
    }

    /**
     * Search all entities (products, categories, brands)
     * 
     * @param SearchRequest $request
     * @return JsonResponse
     */
    public function searchAll(SearchRequest $request): JsonResponse
    {
        $query = $request->getSearchQuery();
        $limit = $request->input('limit', 10);

        $results = $this->searchService->searchAll($query, $limit);

        return $this->successResponse([
            'query' => $query,
            'results' => $results,
        ], 'Search completed successfully');
    }

    /**
     * Get autocomplete suggestions
     * 
     * @param AutocompleteRequest $request
     * @return JsonResponse
     */
    public function autocomplete(AutocompleteRequest $request): JsonResponse
    {
        $query = $request->getSearchQuery();
        $limit = $request->getLimit();

        $results = $this->searchService->getAutocomplete($query, $limit);

        return $this->successResponse([
            'query' => $query,
            'results' => $results,
        ]);
    }

    /**
     * Get search suggestions
     * 
     * @param AutocompleteRequest $request
     * @return JsonResponse
     */
    public function suggestions(AutocompleteRequest $request): JsonResponse
    {
        $query = $request->getSearchQuery();
        $limit = $request->getLimit();

        $suggestions = $this->searchService->getSuggestions($query, $limit);

        return $this->successResponse([
            'query' => $query,
            'suggestions' => $suggestions,
        ]);
    }

    /**
     * Get popular searches
     * 
     * @return JsonResponse
     */
    public function popular(): JsonResponse
    {
        $limit = request()->input('limit', 10);
        
        $popularSearches = $this->searchService->getPopularSearches($limit);

        return $this->successResponse([
            'popular_searches' => $popularSearches,
        ]);
    }

    /**
     * Get trending searches
     * 
     * @return JsonResponse
     */
    public function trending(): JsonResponse
    {
        $days = request()->input('days', 7);
        $limit = request()->input('limit', 10);
        
        $trendingSearches = $this->searchService->getTrendingSearches($days, $limit);

        return $this->successResponse([
            'trending_searches' => $trendingSearches,
            'period_days' => $days,
        ]);
    }

    /**
     * Get user's search history
     * 
     * @return JsonResponse
     */
    public function history(): JsonResponse
    {
        $limit = request()->input('limit', 10);
        
        $history = $this->searchService->getUserSearchHistory($limit);

        return $this->successResponse([
            'search_history' => $history,
        ]);
    }

    /**
     * Clear user's search history
     * 
     * @return JsonResponse
     */
    public function clearHistory(): JsonResponse
    {
        $this->searchService->clearUserSearchHistory();

        return $this->successResponse(
            null,
            'Search history cleared successfully'
        );
    }

    /**
     * Track search interaction (click or conversion)
     * 
     * @param TrackSearchRequest $request
     * @return JsonResponse
     */
    public function track(TrackSearchRequest $request): JsonResponse
    {
        $query = $request->input('query');
        $type = $request->input('type');

        if ($type === 'click') {
            $this->searchService->trackClick($query);
        } elseif ($type === 'conversion') {
            $this->searchService->trackConversion($query);
        }

        return $this->successResponse(
            null,
            'Tracking recorded successfully'
        );
    }

    /**
     * Get available search filters
     * 
     * @param SearchRequest $request
     * @return JsonResponse
     */
    public function filters(SearchRequest $request): JsonResponse
    {
        $query = $request->getSearchQuery();
        $currentFilters = $request->getFilters();

        $availableFilters = $this->searchService->getSearchFilters($query, $currentFilters);

        return $this->successResponse([
            'filters' => $availableFilters,
        ]);
    }
}

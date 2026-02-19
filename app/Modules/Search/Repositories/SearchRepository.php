<?php

namespace App\Modules\Search\Repositories;

use App\Modules\Product\Models\Product;
use App\Modules\Product\Models\Category;
use App\Modules\Product\Models\Brand;
use App\Modules\Search\Models\SearchHistory;
use App\Modules\Search\Models\PopularSearch;
use App\Modules\Search\Models\SearchSynonym;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SearchRepository implements SearchRepositoryInterface
{
    /**
     * Search products with filters using MySQL Full-Text Search
     */
    public function searchProducts(string $query, array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $expandedQuery = $this->expandQueryWithSynonyms($query);
        
        $productQuery = Product::query()
            ->with(['category', 'brand', 'images' => fn($q) => $q->where('is_primary', true)])
            ->active();

        // Full-text search with relevance scoring
        if (!empty($query)) {
            $searchTerms = $this->prepareSearchTerms($expandedQuery);
            
            $productQuery->where(function ($q) use ($searchTerms, $query) {
                // Full-text search with boolean mode for better control
                $q->whereRaw(
                    "MATCH(name, description, short_description, sku) AGAINST(? IN BOOLEAN MODE)",
                    [$searchTerms]
                )
                // Also do LIKE search for partial matches
                ->orWhere('name', 'LIKE', "%{$query}%")
                ->orWhere('sku', 'LIKE', "%{$query}%");
            });

            // Add relevance score for ordering
            $productQuery->selectRaw(
                "*, MATCH(name, description, short_description, sku) AGAINST(? IN NATURAL LANGUAGE MODE) as relevance_score",
                [$expandedQuery]
            );
        }

        // Apply filters
        $this->applyFilters($productQuery, $filters);

        // Default sorting by relevance if searching, otherwise by created_at
        $sortBy = $filters['sort_by'] ?? (!empty($query) ? 'relevance' : 'created_at');
        $sortOrder = $filters['sort_order'] ?? 'desc';

        $this->applySorting($productQuery, $sortBy, $sortOrder);

        return $productQuery->paginate($perPage);
    }

    /**
     * Search across all entities
     */
    public function searchAll(string $query, int $limit = 10): array
    {
        $results = [
            'products' => [],
            'categories' => [],
            'brands' => [],
        ];

        if (empty($query)) {
            return $results;
        }

        // Search products
        $results['products'] = Product::query()
            ->active()
            ->where(function ($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                    ->orWhere('sku', 'LIKE', "%{$query}%");
            })
            ->with(['category:id,name,slug', 'brand:id,name,slug'])
            ->select(['id', 'name', 'slug', 'price', 'compare_price', 'category_id', 'brand_id'])
            ->limit($limit)
            ->get()
            ->map(fn($product) => [
                'id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'price' => $product->price,
                'compare_price' => $product->compare_price,
                'category' => $product->category?->name,
                'brand' => $product->brand?->name,
                'type' => 'product',
            ]);

        // Search categories
        $results['categories'] = Category::query()
            ->active()
            ->where('name', 'LIKE', "%{$query}%")
            ->select(['id', 'name', 'slug', 'icon'])
            ->withCount('products')
            ->limit(5)
            ->get()
            ->map(fn($category) => [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
                'icon' => $category->icon,
                'products_count' => $category->products_count,
                'type' => 'category',
            ]);

        // Search brands
        $results['brands'] = Brand::query()
            ->active()
            ->where('name', 'LIKE', "%{$query}%")
            ->select(['id', 'name', 'slug', 'logo'])
            ->withCount('products')
            ->limit(5)
            ->get()
            ->map(fn($brand) => [
                'id' => $brand->id,
                'name' => $brand->name,
                'slug' => $brand->slug,
                'logo' => $brand->logo,
                'products_count' => $brand->products_count,
                'type' => 'brand',
            ]);

        return $results;
    }

    /**
     * Get search suggestions based on partial query
     */
    public function getSuggestions(string $query, int $limit = 10): Collection
    {
        if (strlen($query) < 2) {
            return collect();
        }

        // Get suggestions from popular searches
        $popularSuggestions = PopularSearch::query()
            ->where('query', 'LIKE', "{$query}%")
            ->orderByDesc('search_count')
            ->limit($limit)
            ->pluck('query');

        // Get suggestions from product names
        $productSuggestions = Product::query()
            ->active()
            ->where('name', 'LIKE', "{$query}%")
            ->limit($limit)
            ->pluck('name')
            ->map(fn($name) => Str::limit($name, 50));

        // Get suggestions from category names
        $categorySuggestions = Category::query()
            ->active()
            ->where('name', 'LIKE', "{$query}%")
            ->limit(5)
            ->pluck('name');

        // Merge and deduplicate
        return $popularSuggestions
            ->merge($productSuggestions)
            ->merge($categorySuggestions)
            ->unique()
            ->take($limit)
            ->values();
    }

    /**
     * Get autocomplete results with categorized suggestions
     */
    public function getAutocomplete(string $query, int $limit = 8): array
    {
        if (strlen($query) < 2) {
            return [
                'suggestions' => [],
                'products' => [],
                'categories' => [],
                'brands' => [],
            ];
        }

        return [
            'suggestions' => $this->getSuggestions($query, 5)->toArray(),
            'products' => Product::query()
                ->active()
                ->where('name', 'LIKE', "%{$query}%")
                ->select(['id', 'name', 'slug', 'price'])
                ->with(['images' => fn($q) => $q->where('is_primary', true)->select(['product_id', 'image'])])
                ->limit(4)
                ->get()
                ->map(fn($p) => [
                    'id' => $p->id,
                    'name' => $p->name,
                    'slug' => $p->slug,
                    'price' => $p->price,
                    'image' => $p->images->first()?->image,
                ])
                ->toArray(),
            'categories' => Category::query()
                ->active()
                ->where('name', 'LIKE', "%{$query}%")
                ->select(['id', 'name', 'slug'])
                ->limit(3)
                ->get()
                ->toArray(),
            'brands' => Brand::query()
                ->active()
                ->where('name', 'LIKE', "%{$query}%")
                ->select(['id', 'name', 'slug'])
                ->limit(3)
                ->get()
                ->toArray(),
        ];
    }

    /**
     * Save search to history
     */
    public function saveSearchHistory(
        string $query,
        array $filters,
        int $resultsCount,
        ?int $userId,
        ?string $sessionId
    ): void {
        SearchHistory::create([
            'user_id' => $userId,
            'session_id' => $sessionId,
            'query' => $query,
            'filters' => !empty($filters) ? $filters : null,
            'results_count' => $resultsCount,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Get user's search history
     */
    public function getUserSearchHistory(?int $userId, ?string $sessionId, int $limit = 10): Collection
    {
        $query = SearchHistory::query()
            ->select(['query', DB::raw('MAX(created_at) as last_searched_at'), DB::raw('COUNT(*) as search_count')])
            ->groupBy('query')
            ->orderByDesc('last_searched_at')
            ->limit($limit);

        if ($userId) {
            $query->where('user_id', $userId);
        } elseif ($sessionId) {
            $query->where('session_id', $sessionId);
        } else {
            return collect();
        }

        return $query->get();
    }

    /**
     * Clear user's search history
     */
    public function clearUserSearchHistory(?int $userId, ?string $sessionId): void
    {
        $query = SearchHistory::query();

        if ($userId) {
            $query->where('user_id', $userId);
        } elseif ($sessionId) {
            $query->where('session_id', $sessionId);
        } else {
            return;
        }

        $query->delete();
    }

    /**
     * Track popular search
     */
    public function trackPopularSearch(string $query): void
    {
        $normalizedQuery = $this->normalizeQuery($query);
        
        PopularSearch::updateOrCreate(
            ['query' => $normalizedQuery],
            ['last_searched_at' => now()]
        )->increment('search_count');
    }

    /**
     * Get popular searches
     */
    public function getPopularSearches(int $limit = 10): Collection
    {
        return PopularSearch::query()
            ->popular($limit)
            ->get(['query', 'search_count']);
    }

    /**
     * Get trending searches
     */
    public function getTrendingSearches(int $days = 7, int $limit = 10): Collection
    {
        return PopularSearch::query()
            ->trending($days, $limit)
            ->get(['query', 'search_count', 'last_searched_at']);
    }

    /**
     * Track click on search result
     */
    public function trackClick(string $query): void
    {
        $normalizedQuery = $this->normalizeQuery($query);
        
        PopularSearch::where('query', $normalizedQuery)->increment('click_count');
    }

    /**
     * Track conversion from search
     */
    public function trackConversion(string $query): void
    {
        $normalizedQuery = $this->normalizeQuery($query);
        
        PopularSearch::where('query', $normalizedQuery)->increment('conversion_count');
    }

    /*
    |--------------------------------------------------------------------------
    | Private Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Expand query with synonyms
     */
    private function expandQueryWithSynonyms(string $query): string
    {
        $words = explode(' ', strtolower($query));
        $expandedWords = [];

        foreach ($words as $word) {
            $expandedWords[] = $word;
            
            $synonym = SearchSynonym::active()->forTerm($word)->first();
            if ($synonym) {
                $expandedWords = array_merge($expandedWords, $synonym->synonyms);
            }
        }

        return implode(' ', array_unique($expandedWords));
    }

    /**
     * Prepare search terms for boolean mode
     */
    private function prepareSearchTerms(string $query): string
    {
        $words = preg_split('/\s+/', trim($query));
        $terms = [];

        foreach ($words as $word) {
            if (strlen($word) >= 2) {
                // Add wildcard for partial matching
                $terms[] = '+' . $word . '*';
            }
        }

        return implode(' ', $terms);
    }

    /**
     * Apply filters to product query
     */
    private function applyFilters($query, array $filters): void
    {
        // Category filter
        if (!empty($filters['category'])) {
            $category = Category::where('slug', $filters['category'])->first();
            if ($category) {
                $categoryIds = $category->descendants()->pluck('id')->push($category->id);
                $query->whereIn('category_id', $categoryIds);
            }
        }

        // Brand filter
        if (!empty($filters['brand'])) {
            if (is_array($filters['brand'])) {
                $brandIds = Brand::whereIn('slug', $filters['brand'])->pluck('id');
                $query->whereIn('brand_id', $brandIds);
            } else {
                $brand = Brand::where('slug', $filters['brand'])->first();
                if ($brand) {
                    $query->where('brand_id', $brand->id);
                }
            }
        }

        // Price range filter
        if (!empty($filters['min_price'])) {
            $query->where('price', '>=', $filters['min_price']);
        }

        if (!empty($filters['max_price'])) {
            $query->where('price', '<=', $filters['max_price']);
        }

        // In stock filter
        if (!empty($filters['in_stock'])) {
            $query->where('quantity', '>', 0);
        }

        // On sale filter
        if (!empty($filters['on_sale'])) {
            $query->whereNotNull('compare_price')
                ->whereColumn('compare_price', '>', 'price');
        }

        // Featured filter
        if (!empty($filters['featured'])) {
            $query->where('is_featured', true);
        }

        // Rating filter
        if (!empty($filters['min_rating'])) {
            $query->where('rating', '>=', $filters['min_rating']);
        }
    }

    /**
     * Apply sorting to query
     */
    private function applySorting($query, string $sortBy, string $sortOrder): void
    {
        $validSortFields = ['price', 'name', 'created_at', 'rating', 'relevance', 'popularity'];
        $sortOrder = in_array($sortOrder, ['asc', 'desc']) ? $sortOrder : 'desc';

        if (!in_array($sortBy, $validSortFields)) {
            $sortBy = 'created_at';
        }

        switch ($sortBy) {
            case 'relevance':
                $query->orderByDesc('relevance_score');
                break;
            case 'popularity':
                $query->orderByDesc('view_count');
                break;
            case 'price':
                $query->orderBy('price', $sortOrder);
                break;
            case 'name':
                $query->orderBy('name', $sortOrder);
                break;
            case 'rating':
                $query->orderByDesc('rating');
                break;
            default:
                $query->orderBy('created_at', $sortOrder);
        }
    }

    /**
     * Normalize search query
     */
    private function normalizeQuery(string $query): string
    {
        return strtolower(trim(preg_replace('/\s+/', ' ', $query)));
    }
}

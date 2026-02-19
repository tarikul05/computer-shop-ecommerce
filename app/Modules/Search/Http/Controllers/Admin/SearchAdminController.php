<?php

namespace App\Modules\Search\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use App\Modules\Search\Models\SearchSynonym;
use App\Modules\Search\Models\PopularSearch;
use App\Modules\Search\Models\SearchHistory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SearchAdminController extends Controller
{
    use ApiResponse;

    /**
     * Get search analytics dashboard
     */
    public function analytics(): JsonResponse
    {
        $today = now()->startOfDay();
        $weekAgo = now()->subDays(7)->startOfDay();
        $monthAgo = now()->subDays(30)->startOfDay();

        $analytics = [
            'total_searches' => [
                'today' => SearchHistory::where('created_at', '>=', $today)->count(),
                'this_week' => SearchHistory::where('created_at', '>=', $weekAgo)->count(),
                'this_month' => SearchHistory::where('created_at', '>=', $monthAgo)->count(),
                'all_time' => SearchHistory::count(),
            ],
            'unique_queries' => [
                'today' => SearchHistory::where('created_at', '>=', $today)->distinct('query')->count('query'),
                'this_week' => SearchHistory::where('created_at', '>=', $weekAgo)->distinct('query')->count('query'),
                'this_month' => SearchHistory::where('created_at', '>=', $monthAgo)->distinct('query')->count('query'),
            ],
            'average_results' => [
                'today' => round(SearchHistory::where('created_at', '>=', $today)->avg('results_count') ?? 0, 1),
                'this_week' => round(SearchHistory::where('created_at', '>=', $weekAgo)->avg('results_count') ?? 0, 1),
            ],
            'zero_result_searches' => [
                'today' => SearchHistory::where('created_at', '>=', $today)->where('results_count', 0)->count(),
                'this_week' => SearchHistory::where('created_at', '>=', $weekAgo)->where('results_count', 0)->count(),
            ],
            'top_searches' => PopularSearch::orderByDesc('search_count')->limit(10)->get(['query', 'search_count', 'click_count']),
            'recent_zero_results' => SearchHistory::where('results_count', 0)
                ->orderByDesc('created_at')
                ->limit(10)
                ->get(['query', 'created_at']),
        ];

        return $this->successResponse($analytics, 'Search analytics retrieved successfully');
    }

    /**
     * Get search performance metrics
     */
    public function performance(): JsonResponse
    {
        $metrics = PopularSearch::query()
            ->selectRaw('
                SUM(search_count) as total_searches,
                SUM(click_count) as total_clicks,
                SUM(conversion_count) as total_conversions,
                AVG(CASE WHEN search_count > 0 THEN (click_count * 100.0 / search_count) ELSE 0 END) as avg_ctr,
                AVG(CASE WHEN click_count > 0 THEN (conversion_count * 100.0 / click_count) ELSE 0 END) as avg_conversion_rate
            ')
            ->first();

        $topPerformers = PopularSearch::query()
            ->where('search_count', '>=', 10)
            ->withConversions()
            ->orderByRaw('(conversion_count * 1.0 / search_count) DESC')
            ->limit(10)
            ->get(['query', 'search_count', 'click_count', 'conversion_count']);

        return $this->successResponse([
            'overview' => [
                'total_searches' => $metrics->total_searches ?? 0,
                'total_clicks' => $metrics->total_clicks ?? 0,
                'total_conversions' => $metrics->total_conversions ?? 0,
                'average_ctr' => round($metrics->avg_ctr ?? 0, 2) . '%',
                'average_conversion_rate' => round($metrics->avg_conversion_rate ?? 0, 2) . '%',
            ],
            'top_performers' => $topPerformers,
        ], 'Search performance metrics retrieved successfully');
    }

    /**
     * Get all synonyms
     */
    public function synonyms(): JsonResponse
    {
        $synonyms = SearchSynonym::orderBy('term')->get();

        return $this->successResponse([
            'synonyms' => $synonyms,
            'total' => $synonyms->count(),
        ]);
    }

    /**
     * Create a new synonym
     */
    public function storeSynonym(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'term' => ['required', 'string', 'max:100', 'unique:search_synonyms,term'],
            'synonyms' => ['required', 'array', 'min:1'],
            'synonyms.*' => ['required', 'string', 'max:100'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $synonym = SearchSynonym::create([
            'term' => strtolower(trim($validated['term'])),
            'synonyms' => array_map(fn($s) => strtolower(trim($s)), $validated['synonyms']),
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return $this->successResponse($synonym, 'Synonym created successfully', 201);
    }

    /**
     * Update a synonym
     */
    public function updateSynonym(Request $request, SearchSynonym $synonym): JsonResponse
    {
        $validated = $request->validate([
            'term' => ['sometimes', 'required', 'string', 'max:100', 'unique:search_synonyms,term,' . $synonym->id],
            'synonyms' => ['sometimes', 'required', 'array', 'min:1'],
            'synonyms.*' => ['required', 'string', 'max:100'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data = [];
        
        if (isset($validated['term'])) {
            $data['term'] = strtolower(trim($validated['term']));
        }
        
        if (isset($validated['synonyms'])) {
            $data['synonyms'] = array_map(fn($s) => strtolower(trim($s)), $validated['synonyms']);
        }
        
        if (isset($validated['is_active'])) {
            $data['is_active'] = $validated['is_active'];
        }

        $synonym->update($data);

        return $this->successResponse($synonym->fresh(), 'Synonym updated successfully');
    }

    /**
     * Delete a synonym
     */
    public function destroySynonym(SearchSynonym $synonym): JsonResponse
    {
        $synonym->delete();

        return $this->successResponse(null, 'Synonym deleted successfully');
    }

    /**
     * Bulk import synonyms
     */
    public function importSynonyms(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'synonyms' => ['required', 'array', 'min:1'],
            'synonyms.*.term' => ['required', 'string', 'max:100'],
            'synonyms.*.synonyms' => ['required', 'array', 'min:1'],
            'synonyms.*.synonyms.*' => ['required', 'string', 'max:100'],
        ]);

        $imported = 0;
        $skipped = 0;

        foreach ($validated['synonyms'] as $item) {
            $term = strtolower(trim($item['term']));
            
            $existing = SearchSynonym::where('term', $term)->first();
            
            if ($existing) {
                $skipped++;
                continue;
            }

            SearchSynonym::create([
                'term' => $term,
                'synonyms' => array_map(fn($s) => strtolower(trim($s)), $item['synonyms']),
                'is_active' => true,
            ]);
            
            $imported++;
        }

        return $this->successResponse([
            'imported' => $imported,
            'skipped' => $skipped,
        ], "Imported {$imported} synonyms, skipped {$skipped} duplicates");
    }

    /**
     * Get popular search queries
     */
    public function popularQueries(Request $request): JsonResponse
    {
        $perPage = $request->input('per_page', 20);
        $sortBy = $request->input('sort_by', 'search_count');
        $sortOrder = $request->input('sort_order', 'desc');

        $validSortFields = ['search_count', 'click_count', 'conversion_count', 'last_searched_at', 'query'];

        if (!in_array($sortBy, $validSortFields)) {
            $sortBy = 'search_count';
        }

        $queries = PopularSearch::orderBy($sortBy, $sortOrder)->paginate($perPage);

        return $this->successResponse($queries);
    }

    /**
     * Get zero-result search queries
     */
    public function zeroResultQueries(Request $request): JsonResponse
    {
        $perPage = $request->input('per_page', 20);
        $days = $request->input('days', 30);

        $queries = SearchHistory::query()
            ->select('query', DB::raw('COUNT(*) as count'), DB::raw('MAX(created_at) as last_searched'))
            ->where('results_count', 0)
            ->where('created_at', '>=', now()->subDays($days))
            ->groupBy('query')
            ->orderByDesc('count')
            ->paginate($perPage);

        return $this->successResponse([
            'queries' => $queries,
            'period_days' => $days,
        ]);
    }

    /**
     * Reset popular search statistics
     */
    public function resetPopularStats(Request $request): JsonResponse
    {
        $type = $request->input('type', 'all');

        if ($type === 'clicks') {
            PopularSearch::query()->update(['click_count' => 0]);
            $message = 'Click counts reset successfully';
        } elseif ($type === 'conversions') {
            PopularSearch::query()->update(['conversion_count' => 0]);
            $message = 'Conversion counts reset successfully';
        } else {
            PopularSearch::query()->update([
                'search_count' => 0,
                'click_count' => 0,
                'conversion_count' => 0,
            ]);
            $message = 'All search statistics reset successfully';
        }

        return $this->successResponse(null, $message);
    }

    /**
     * Cleanup old search history
     */
    public function cleanupHistory(Request $request): JsonResponse
    {
        $days = $request->input('days', 90);
        
        $deleted = SearchHistory::where('created_at', '<', now()->subDays($days))->delete();

        return $this->successResponse([
            'deleted_records' => $deleted,
            'older_than_days' => $days,
        ], "Deleted {$deleted} old search history records");
    }
}

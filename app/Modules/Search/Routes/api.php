<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Search\Http\Controllers\SearchController;
use App\Modules\Search\Http\Controllers\Admin\SearchAdminController;

/*
|--------------------------------------------------------------------------
| Search Module API Routes
|--------------------------------------------------------------------------
*/

// Public search routes
Route::prefix('search')->name('search.')->group(function () {
    // Main product search with filters
    Route::get('/', [SearchController::class, 'search'])->name('products');
    
    // Search all entities (products, categories, brands)
    Route::get('/all', [SearchController::class, 'searchAll'])->name('all');
    
    // Autocomplete for search box
    Route::get('/autocomplete', [SearchController::class, 'autocomplete'])->name('autocomplete');
    
    // Search suggestions
    Route::get('/suggestions', [SearchController::class, 'suggestions'])->name('suggestions');
    
    // Popular searches
    Route::get('/popular', [SearchController::class, 'popular'])->name('popular');
    
    // Trending searches
    Route::get('/trending', [SearchController::class, 'trending'])->name('trending');
    
    // Available filters
    Route::get('/filters', [SearchController::class, 'filters'])->name('filters');
    
    // Track search interactions (click, conversion)
    Route::post('/track', [SearchController::class, 'track'])->name('track');
});

// Authenticated user search routes
Route::middleware('auth:sanctum')->prefix('search')->name('search.')->group(function () {
    // User's search history
    Route::get('/history', [SearchController::class, 'history'])->name('history');
    
    // Clear user's search history
    Route::delete('/history', [SearchController::class, 'clearHistory'])->name('history.clear');
});

// Admin search management routes
Route::middleware(['auth:sanctum', 'admin'])->prefix('admin/search')->name('admin.search.')->group(function () {
    // Analytics & Metrics
    Route::get('/analytics', [SearchAdminController::class, 'analytics'])->name('analytics');
    Route::get('/performance', [SearchAdminController::class, 'performance'])->name('performance');
    
    // Popular queries management
    Route::get('/queries/popular', [SearchAdminController::class, 'popularQueries'])->name('queries.popular');
    Route::get('/queries/zero-results', [SearchAdminController::class, 'zeroResultQueries'])->name('queries.zero-results');
    
    // Synonyms management
    Route::get('/synonyms', [SearchAdminController::class, 'synonyms'])->name('synonyms.index');
    Route::post('/synonyms', [SearchAdminController::class, 'storeSynonym'])->name('synonyms.store');
    Route::put('/synonyms/{synonym}', [SearchAdminController::class, 'updateSynonym'])->name('synonyms.update');
    Route::delete('/synonyms/{synonym}', [SearchAdminController::class, 'destroySynonym'])->name('synonyms.destroy');
    Route::post('/synonyms/import', [SearchAdminController::class, 'importSynonyms'])->name('synonyms.import');
    
    // Maintenance
    Route::post('/reset-stats', [SearchAdminController::class, 'resetPopularStats'])->name('reset-stats');
    Route::post('/cleanup-history', [SearchAdminController::class, 'cleanupHistory'])->name('cleanup-history');
});

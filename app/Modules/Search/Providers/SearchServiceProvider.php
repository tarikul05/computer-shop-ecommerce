<?php

namespace App\Modules\Search\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use App\Modules\Search\Repositories\SearchRepository;
use App\Modules\Search\Repositories\SearchRepositoryInterface;
use App\Modules\Search\Services\SearchService;

class SearchServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(SearchRepositoryInterface::class, SearchRepository::class);
        $this->app->singleton(SearchService::class);
    }

    public function boot(): void
    {
        $this->registerRoutes();
        $this->loadMigrationsFrom(__DIR__ . '/../../../database/migrations');
    }

    protected function registerRoutes(): void
    {
        Route::prefix('api/v1')
            ->middleware('api')
            ->group(__DIR__ . '/../Routes/api.php');
    }
}

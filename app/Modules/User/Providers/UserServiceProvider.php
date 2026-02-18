<?php

namespace App\Modules\User\Providers;

use Illuminate\Support\ServiceProvider;

class UserServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->loadRoutes();
    }

    /**
     * Load module routes
     */
    protected function loadRoutes(): void
    {
        $routePath = __DIR__ . '/../Routes/api.php';
        
        if (file_exists($routePath)) {
            \Illuminate\Support\Facades\Route::prefix('api/v1')
                ->middleware('api')
                ->group($routePath);
        }
    }
}

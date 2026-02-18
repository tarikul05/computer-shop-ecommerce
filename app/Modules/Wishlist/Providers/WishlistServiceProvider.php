<?php

namespace App\Modules\Wishlist\Providers;

use Illuminate\Support\ServiceProvider;

class WishlistServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->loadRoutes();
    }

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

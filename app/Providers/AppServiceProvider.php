<?php

namespace App\Providers;

use App\Models\Brand;
use App\Models\Categories;
use App\Models\Product;
use App\Models\SubCategories;
use App\View\Composers\ClientComposer;
use App\View\Composers\SidebarComposer;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use RuntimeException;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (config('session.driver') !== 'database') {
            throw new RuntimeException('Invalid SESSION_DRIVER. This application requires database sessions.');
        }

        // Force HTTPS in production to fix Mixed Content errors
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
        View::composer('client.partials.sidebar', SidebarComposer::class);
        View::composer('client.*', ClientComposer::class);

        // Map the database ENUM values to actual Model classes
        Relation::morphMap([
            'brand'       => Brand::class,
            'category'    => Categories::class,
            'subcategory' => SubCategories::class,
            'product'     => Product::class,
        ]);
    }
}

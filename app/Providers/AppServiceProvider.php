<?php

namespace App\Providers;

use App\View\Composers\ClientComposer;
use App\View\Composers\SidebarComposer;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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
        // Force HTTPS in production to fix Mixed Content errors
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
        View::composer('client.partials.sidebar', SidebarComposer::class);
        View::composer('client.*', ClientComposer::class);

        // Map the database ENUM values to actual Model classes
        Relation::morphMap([
            'product'     => \App\Models\Product::class,
            'category'    => \App\Models\Categories::class, // Check if your model is Category or Categories
            'subcategory' => \App\Models\SubCategories::class,
            'brand'       => \App\Models\Brand::class,
        ]);
    }
}

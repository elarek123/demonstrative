<?php

namespace App\Providers;

use App\Models\Geo;
use App\Models\Lead;
use App\Models\Product;
use App\Models\ProductGeo;
use App\Observers\GeoObserver;
use App\Observers\LeadObserver;
use App\Observers\ProductObserver;
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
        Geo::observe(GeoObserver::class);
        Product::observe(ProductObserver::class);
        Lead::observe(LeadObserver::class);
    }
}

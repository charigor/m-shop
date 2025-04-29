<?php

namespace App\Providers;

use App\Services\Catalog\CatalogService;
use App\Services\Catalog\CategoryService;
use App\Services\Catalog\ProductService;
use App\Services\Filter\ElasticFilter;
use Illuminate\Support\ServiceProvider;

class CatalogServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(CategoryService::class);

        $this->app->singleton(ProductService::class, function ($app) {
            return new ProductService();
        });

        $this->app->singleton(CatalogService::class, function ($app) {
            return new CatalogService(
                $app->make(CategoryService::class),
                $app->make(ProductService::class),
                $app->make(ElasticFilter::class)
            );
        });
    }
}

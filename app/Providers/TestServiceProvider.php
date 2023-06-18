<?php

namespace App\Providers;

use App\Services\Test\TestService;
use Illuminate\Support\ServiceProvider;

class TestServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(TestService::class,function($app){
            return new TestService(350);
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {

    }
}

<?php

namespace App\Providers;

use App\Models\Lang;
use App\Services\PrivateService;
use App\Services\StripeService;
use App\Services\Test\TestService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class TestServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {

    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->app->bind(\App\Services\Payment::class, function ($app) {
            $request = app(\Illuminate\Http\Request::class);
            if ($request->payment === 'stripe') {
                return new StripeService();
            }
                return new PrivateService();
        });
        $this->app->singleton('shopLanguages',function($app) {
            return Lang::whereActive(1)->get()->pluck('code');
        });
        View::share( 'shopLanguages', Lang::whereActive(1)->get()->pluck('code') );


    }
}

<?php

namespace App\Providers;

use App\Models\Cart;
use App\Models\Category;
use App\Services\Contracts\CartInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public $singletons = [
        Cart::class => CartInterface::class,
    ];
    /**
     * Register any application services.
     */
    public function register(): void
    {

        if ($this->app->environment('local')) {
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        $this->app->bind(CartInterface::class, function () {
            return new Cart();
        });

        View::share( 'categories', Category::with(['media'])
                                            ->selectRaw(
                                                'categories.*,
                                                category_lang.title,
                                                category_lang.locale,
                                                category_lang.description,
                                                category_lang.meta_description,
                                                category_lang.meta_keywords,
                                                category_lang.meta_title,
                                                category_lang.link_rewrite')
                                            ->leftJoin('category_lang', 'category_lang.category_id', '=', 'categories.id')
                                            ->where('locale',app()->getLocale())
                                            ->active()
                                            ->get()->toTree());

     }
}

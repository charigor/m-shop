<?php

namespace App\Providers;

use App\Models\Cart;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use App\Observers\ProductObserver;
use App\Services\Contracts\CartInterface;
use App\Services\Contracts\SearchEngineInterface;
use App\Services\Filter\ProductFilterContract;
use App\Services\Filter\ProductMeilisearchFilter;
use App\Services\Search\ElasticSearch;
use Elastic\Elasticsearch\ClientBuilder;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;
use Laravel\Cashier\Cashier;

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
        $this->app->singleton(\Elastic\Elasticsearch\Client::class, function () {
            return ClientBuilder::create()
                ->setHosts([env('ELASTICSEARCH_HOST', 'http://elasticsearch:9200')])
                ->build();
        });
        $this->app->bind(SearchEngineInterface::class, ElasticSearch::class);
        //        if ($this->app->environment('local')) {
        //            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
        //            $this->app->register(TelescopeServiceProvider::class);
        //
        //        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        Product::observe(ProductObserver::class);

        app()->setLocale(session('locale', config('app.locale')));
        Cashier::useCustomerModel(User::class);
        Cashier::calculateTaxes();
        Cashier::useSubscriptionModel(Subscription::class);
        Cashier::useSubscriptionItemModel(SubscriptionItem::class);

        $this->app->bind(ProductFilterContract::class, function () {
            return new ProductMeilisearchFilter;
        });
        $this->app->bind(CartInterface::class, function () {
            return new Cart;
        });
        if (! $this->app->runningInConsole()) {

            view()->composer('*', function ($view) {
                $categories = Category::with(['children', 'translate'])->get()->toTree();
                $view->with('categories', $categories);
                $view->with('currantLang', session()->has('locale') ? session()->get('locale') : app()->getLocale());
            });
        }
        Collection::macro('recursive', function () {
            return $this->map(function ($value) {
                if (is_array($value) || is_object($value)) {
                    return collect($value)->recursive();
                }

                return $value;
            });
        });
    }
}

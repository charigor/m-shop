<?php

namespace App\Providers;

use App\Http\Livewire\Checkout;
use App\Http\Livewire\CheckoutStepComponent;
use App\Http\Livewire\DeliveryAddressStepComponent;
use App\Http\Livewire\PaymentOrderStepComponent;
use App\Models\Cart;
use App\Models\Category;
use App\Models\Product;
use App\Services\Contracts\CartInterface;
use App\Services\Filter\ElasticSearchRepository;
use App\Services\Filter\ProductFilterContract;
use App\Services\Filter\ProductMeilisearchFilter;
use Elastic\Elasticsearch\Client;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use GuzzleHttp\Client as HttpClient;
use Livewire\Livewire;

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
//        if ($this->app->environment('local')) {
//            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
//            $this->app->register(TelescopeServiceProvider::class);
//
//        }
//        $this->app->bind(\App\Services\Filter\BrandSearchRepository::class, function ($app) {
//            /*This is useful in case we want to turn-off our
//            search cluster or when deploying the search
//            to a live, running application at first.*/
////                if (! config('services.search.enabled')) {
////                    return new \App\Services\Filter\BrandSearchRepository::class();
////                }
//            return new \App\Services\Filter\ElasticSearchRepository(
//                $app->make(Client::class)
//            );
//        });
//        $this->bindSearchClient();
    }
//    private function bindSearchClient()
//    {

//        $this->app->bind(Client::class, function ($app) {
//            return \Elastic\Elasticsearch\ClientBuilder::create()->setSSLVerification(false)
////                ->setHttpClient(new HttpClient(['verify' => false ]))
////                ->setHosts($app['config']->get('services.search.hosts'))
//                ->setHosts(["http://elastic:9200"])
//                ->build();
//        });
//    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        Livewire::component('checkout-wizard', Checkout::class);
        Livewire::component('checkout-step', CheckoutStepComponent::class);
        Livewire::component('checkout-delivery-step', DeliveryAddressStepComponent::class);
        Livewire::component('checkout-payment-step', PaymentOrderStepComponent::class);

        $this->app->bind(ProductFilterContract::class, function () {
            return new ProductMeilisearchFilter();
        });
        $this->app->bind(CartInterface::class, function () {
            return new Cart();
        });
        if (!$this->app->runningInConsole()) {

            view()->composer('*', function($view) {
                $categories = Category::with(['children','translate'])->get()->toTree();
                $view->with('categories', $categories);
                $view->with('currantLang', session()->has('locale') ? session()->get('locale') : app()->getLocale());
            });
//            View::share('categories', Category::with(['media'])
//                ->selectRaw(
//                    'categories.*,
//                                                category_lang.title,
//                                                category_lang.locale,
//                                                category_lang.description,
//                                                category_lang.meta_description,
//                                                category_lang.meta_keywords,
//                                                category_lang.meta_title,
//                                                category_lang.link_rewrite')
//                ->leftJoin('category_lang', 'category_lang.category_id', '=', 'categories.id')
//                ->where('locale', app()->getLocale())
//                ->active()
//                ->get()->toTree());

        }
        Collection::macro('recursive',function(){
            return $this->map(function($value) {
                if(is_array($value) || is_object($value))
                {
                    return collect($value)->recursive();
                }
                return $value;
            });
        });
    }
}

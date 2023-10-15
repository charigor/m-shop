<?php

namespace App\Observers;

use App\Events\ProductUpdateIndex;
use App\Models\Product;
use Illuminate\Support\Facades\Artisan;

class ProductObserver
{
    /**
     * Handle the Product "created" event.
     */
    public function created(Product $product): void
    {
        ProductUpdateIndex::dispatch($product);
    }

    /**
     * Handle the Product "updated" event.
     */
    public function updated(Product $product): void
    {
        info('hererererer');
        ProductUpdateIndex::dispatch($product);
//        Artisan::call('app:reindex-meilisearch-product', [
//            'id' => $product->id
//        ]);
//        Artisan::call('search:product.update');

    }

    /**
     * Handle the Product "deleted" event.
     */
    public function deleted(Product $product): void
    {
        ProductUpdateIndex::dispatch($product);
        info('deleted');
    }

    /**
     * Handle the Product "restored" event.
     */
    public function restored(Product $product): void
    {
        info('restored');
    }

    /**
     * Handle the Product "force deleted" event.
     */
    public function forceDeleted(Product $product): void
    {
        info('forceDeleted');
    }
}

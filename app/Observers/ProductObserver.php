<?php

namespace App\Observers;

use App\Models\Product;

class ProductObserver
{
    /**
     * Handle the Product "created" event.
     */
    public function creating(Product $product): void
    {
        $product->cost = ($product->price - $product->rebate);
    }

    public function updating(Product $product): void
    {
        $product->cost = ($product->price - $product->rebate);

    }
}

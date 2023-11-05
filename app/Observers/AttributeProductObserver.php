<?php

namespace App\Observers;

use App\Models\AttributeProduct;
use App\Models\Product;

class AttributeProductObserver
{
    /**
     * Handle the Product "created" event.
     */
    public function creating(AttributeProduct $attributeProduct): void
    {
        $attributeProduct->cost = ($attributeProduct->price - $attributeProduct->rebate);
    }

    /**
     * @param  Product  $product
     */
    public function updating(AttributeProduct $attributeProduct): void
    {
        $attributeProduct->cost = ($attributeProduct->price - $attributeProduct->rebate);

    }
}

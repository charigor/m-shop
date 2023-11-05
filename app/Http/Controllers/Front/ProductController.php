<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Product;

class ProductController extends Controller
{
    public function show($slug)
    {
        $product = Product::with(['features', 'features.feature', 'attributes.attributes'])->whereHas('translate', fn ($q) => $q->where('link_rewrite', $slug))->active()->first();

        return view('front.product.show', compact('product'));
    }
}

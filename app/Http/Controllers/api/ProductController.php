<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function show($link_rewrite, Request $request): ProductResource
    {
        $currentLocale = $request->get('locale', app()->getLocale());
        $fallbackLocale = config('app.fallback_locale');

        $product = Product::with(['attributes.attributes', 'media', 'translateWithFallback' => function ($query) use ($currentLocale, $fallbackLocale) {
            $query->where('locale', $currentLocale)
                ->orWhere('locale', $fallbackLocale);
        }])
            ->whereHas('translateWithFallback', function ($query) use ($link_rewrite) {
                $query->where('link_rewrite', $link_rewrite);
            })
            ->firstOrFail();

        return new ProductResource($product);
    }
}

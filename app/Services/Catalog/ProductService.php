<?php

namespace App\Services\Catalog;

use App\Models\Product;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductService
{
    /**
     * Get filtered products
     */
    public function getFilteredProducts(array $facet, string $locale, string $sort): LengthAwarePaginator
    {
        $fallbackLocale = config('app.fallback_locale');

        $productsQuery = Product::with(['attributes.attributes', 'media', 'translateWithFallback' => function ($query) use ($locale, $fallbackLocale) {
            $query->where('locale', $locale)
                ->orWhere('locale', $fallbackLocale);
        }]);

        // Only include products that have at least one translation (either in requested locale or fallback)
        $productsQuery->whereHas('translateWithFallback');

        // Apply filtering by ID from faceted search
        if (! empty($facet['productIds'])) {
            $productsQuery->whereIn('id', $facet['productIds']);
        }

        // Apply sorting
        $this->applySorting($productsQuery, $sort);

        // Execute query with pagination
        return $productsQuery->paginate(CategoryService::PER_PAGE);
    }

    /**
     * Apply sorting to product query
     */
    private function applySorting($query, string $sort): void
    {
        $sortOptions = [
            'price_asc' => ['price', 'asc'],
            'price_desc' => ['price', 'desc'],
            'newest' => ['created_at', 'desc'],
        ];

        [$column, $direction] = $sortOptions[$sort] ?? ['id', 'desc'];
        $query->orderBy($column, $direction);
    }
}

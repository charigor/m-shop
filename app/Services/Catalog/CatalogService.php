<?php

namespace App\Services\Catalog;

use App\Http\Resources\Api\CategoryResource;
use App\Http\Resources\Api\ProductResource;
use App\Services\Filter\Elastic\Filter;
use Illuminate\Http\JsonResponse;

class CatalogService
{
    protected CategoryService $categoryService;

    protected ProductService $productService;

    protected Filter $filterService;

    public function __construct(CategoryService $categoryService, ProductService $productService, Filter $filterService)
    {
        $this->categoryService = $categoryService;
        $this->productService = $productService;
        $this->filterService = $filterService;
    }

    /**
     * Get catalog data
     */
    public function getCatalog(?string $link_rewrite, array $filters, string $locale, string $sort): JsonResponse
    {
        // Get category and subcategories
        $categoryData = $this->categoryService->getCategoryData($link_rewrite, $locale);
        $category = $categoryData['category'] ?? null;
        $subcategories = $categoryData['subcategories'] ?? [];

        // If category not found and category is required, return empty result
        if ($link_rewrite && ! $category) {
            return response()->json([
                'category' => null,
                'subcategories' => [],
                'products' => [],
                'facet' => [],
                'pagination' => $this->categoryService->emptyPagination(),
            ]);
        }

        // Get facet search
        $categoryId = $category ? $category->id : null;
        $facet = $this->filterService->handle($categoryId, $filters);

        // Get filtered products
        $products = $this->productService->getFilteredProducts($facet, $locale, $sort);

        // Form the response
        return response()->json([
            'category' => $category ? new CategoryResource($category) : null,
            'subcategories' => CategoryResource::collection($subcategories),
            'products' => ProductResource::collection($products),
            'facet' => $facet,
            'pagination' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
            ],
        ]);
    }
}

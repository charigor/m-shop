<?php

namespace App\Services\Catalog;

use App\Models\Category;

class CategoryService
{
    public const PER_PAGE = 25;

    /**
     * Get data about category and subcategories
     */
    public function getCategoryData(?string $link_rewrite, string $locale): array
    {
        if (! $link_rewrite) {
            return ['category' => null, 'subcategories' => []];
        }

        $category = Category::with(['media', 'translate'])
            ->select('categories.*')
            ->leftJoin('category_lang', 'category_lang.category_id', '=', 'categories.id')
            ->where('link_rewrite', $link_rewrite)
            ->first();

        if (! $category) {
            return ['category' => null, 'subcategories' => []];
        }

        $subcategories = Category::with(['media', 'translate'])
            ->select('categories.*')
            ->leftJoin('category_lang', 'category_lang.category_id', '=', 'categories.id')
            ->where('locale', $locale)
            ->where('parent_id', $category->id)
            ->get();

        return ['category' => $category, 'subcategories' => $subcategories];
    }

    /**
     * Empty pagination for response without data
     */
    public function emptyPagination(): array
    {
        return [
            'current_page' => 1,
            'last_page' => 1,
            'per_page' => self::PER_PAGE,
            'total' => 0,
        ];
    }
}

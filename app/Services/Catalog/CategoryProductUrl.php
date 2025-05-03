<?php

namespace App\Services\Catalog;

use App\Models\Category;
use App\Models\Product;

class CategoryProductUrl
{
    /**
     * Получает полный локализованный URL для категории или продукта
     */
    public function getLocalizedUrl(string $currentSlug, string $targetLocale): array
    {
        // Определяем, является ли последняя часть URL категорией или продуктом
        $pathParts = explode('/', trim($currentSlug, '/'));
        $lastSlug = end($pathParts);

        // Проверяем, является ли последняя часть категорией
        $isCategory = $this->isCategorySlug($lastSlug);

        if ($isCategory) {
            return $this->getLocalizedCategoryPath($currentSlug, $targetLocale);
        } else {
            return $this->getLocalizedProductPath($currentSlug, $targetLocale);
        }
    }

    /**
     * Проверяет, является ли слаг категорией
     */
    public function isCategorySlug(string $slug): bool
    {
        return Category::select('categories.id')
            ->leftJoin('category_lang', 'category_lang.category_id', '=', 'categories.id')
            ->where('link_rewrite', $slug)
            ->exists();
    }

    /**
     * Получает локализованный иерархический путь для категории
     */
    private function getLocalizedCategoryPath(string $currentPath, string $targetLocale): array
    {
        $pathParts = explode('/', trim($currentPath, '/'));
        $lastSlug = end($pathParts);

        // Находим целевую категорию
        $category = Category::select('categories.*')
            ->leftJoin('category_lang', 'category_lang.category_id', '=', 'categories.id')
            ->where('link_rewrite', $lastSlug)
            ->first();

        if (! $category) {
            return [
                'success' => false,
                'error' => 'Category not found',
                'status' => 404,
            ];
        }

        // Создаем локализованный путь, начиная с корня
        $localizedPathParts = [];

        // Получаем всю иерархию категорий
        $ancestors = $category->ancestors()->get();
        foreach ($ancestors as $ancestor) {
            $localizedSlug = $ancestor->translate($targetLocale)->value('link_rewrite');
            if ($localizedSlug) {
                $localizedPathParts[] = $localizedSlug;
            }
        }

        // Добавляем локализованный слаг текущей категории
        $localizedSlug = $category->translate($targetLocale)->value('link_rewrite') ?? $lastSlug;
        $localizedPathParts[] = $localizedSlug;

        $localizedPath = implode('/', $localizedPathParts);

        return [
            'success' => true,
            'localized_slug' => $localizedPath,
            'type' => 'category',
            'object_id' => $category->id,
        ];
    }

    /**
     * Получает локализованный иерархический путь для продукта
     */
    private function getLocalizedProductPath(string $currentPath, string $targetLocale): array
    {
        $pathParts = explode('/', trim($currentPath, '/'));
        $lastSlug = end($pathParts);

        // Находим продукт
        $product = Product::select('products.*')
            ->leftJoin('product_lang', 'product_lang.product_id', '=', 'products.id')
            ->where('link_rewrite', $lastSlug)
            ->first();

        if (! $product) {
            return [
                'success' => false,
                'error' => 'Product not found',
                'status' => 404,
            ];
        }

        // Создаем локализованный путь, начиная с категории продукта
        $localizedPathParts = [];

        // Получаем основную категорию продукта (здесь может потребоваться корректировка)
        $defaultCategory = $product->categories()->first();

        if ($defaultCategory) {
            // Получаем всю иерархию категорий
            $ancestors = $defaultCategory->ancestors()->get();
            foreach ($ancestors as $ancestor) {
                $localizedSlug = $ancestor->translate($targetLocale)->value('link_rewrite');
                if ($localizedSlug) {
                    $localizedPathParts[] = $localizedSlug;
                }
            }

            // Добавляем локализованный слаг основной категории
            $localizedCategorySlug = $defaultCategory->translate($targetLocale)->value('link_rewrite');
            if ($localizedCategorySlug) {
                $localizedPathParts[] = $localizedCategorySlug;
            }
        }

        // Добавляем локализованный слаг продукта
        $localizedProductSlug = $product->translate($targetLocale)->value('link_rewrite') ?? $lastSlug;
        $localizedPathParts[] = $localizedProductSlug;

        $localizedPath = implode('/', $localizedPathParts);

        return [
            'success' => true,
            'localized_slug' => $localizedPath,
            'type' => 'product',
            'object_id' => $product->id,
        ];
    }
}

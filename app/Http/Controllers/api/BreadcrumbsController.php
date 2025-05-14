<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class BreadcrumbsController extends Controller
{
    /**
     * Get all translations for breadcrumbs
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getBreadcrumbsTranslations(Request $request)
    {
        $locale = $request->query('locale', app()->getLocale());

        // Используем кэширование для оптимизации
        $cacheKey = "breadcrumbs_translations_{$locale}";

        return Cache::remember($cacheKey, now()->addHours(24), function () use ($locale) {
            // Получаем все переводы категорий
            $categoryTranslations = DB::table('category_lang')
                ->where('locale', $locale)
                ->select('category_id', 'title', 'link_rewrite')
                ->get()
                ->mapWithKeys(function ($item) {
                    return [$item->link_rewrite => $item->title];
                });

            // Получаем все переводы продуктов
            $productTranslations = DB::table('product_lang')
                ->where('locale', $locale)
                ->select('product_id', 'name', 'link_rewrite')
                ->get()
                ->mapWithKeys(function ($item) {
                    return [$item->link_rewrite => $item->name];
                });

            // Добавляем стандартные переводы для статических страниц
            $staticTranslations = [
                'home' => __('Home', [], $locale),
                'catalog' => __('Catalog', [], $locale),
                'contact' => __('Contact', [], $locale),
                'about' => __('About', [], $locale),
                'search' => __('Search', [], $locale),
                'cart' => __('Cart', [], $locale),
                'account' => __('My Account', [], $locale),
                'orders' => __('Orders', [], $locale),
                'wishlist' => __('Wishlist', [], $locale),
                'login' => __('Login', [], $locale),
                'register' => __('Register', [], $locale),
            ];

            // Объединяем все переводы
            $translations = array_merge(
                $staticTranslations,
                $categoryTranslations->toArray(),
                $productTranslations->toArray()
            );

            return response()->json($translations);
        });
    }

    /**
     * Resolve a URL path to breadcrumbs structure
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function resolvePath(Request $request)
    {
        $path = $request->input('path');
        $locale = $request->input('locale', app()->getLocale());
        if (! $path) {
            return response()->json(['error' => 'Path is required'], 400);
        }

        // Разбиваем путь на сегменты
        $segments = collect(explode('/', trim($path, '/')));

        // Проверяем, является ли первый сегмент языковым кодом
        $isLocalePath = $segments->isNotEmpty() && in_array($segments->first(), ['en', 'uk', 'ru']);
        $languagePrefix = $isLocalePath ? $segments->first() : null;

        // Пропускаем языковой сегмент, если он есть
        if ($isLocalePath) {
            $segments = $segments->slice(1);
        }

        // Если путь пустой после удаления языкового префикса, возвращаем только домашнюю страницу
        if ($segments->isEmpty()) {
            return response()->json([
                [
                    'name' => __('Home', [], $locale),
                    'path' => $isLocalePath ? "/{$languagePrefix}" : '/',
                    'type' => 'static',
                ],
            ]);
        }

        $breadcrumbs = [];
        $currentPath = $isLocalePath ? "/{$languagePrefix}" : '';

        // Добавляем домашнюю страницу в начало
        $breadcrumbs[] = [
            'name' => __('Home', [], $locale),
            'path' => $currentPath ?: '/',
            'type' => 'static',
        ];

        foreach ($segments as $index => $segment) {
            $currentPath .= '/'.$segment;

            // Проверяем, является ли сегмент категорией
            $category = DB::table('category_lang')
                ->where('link_rewrite', $segment)
                ->where('locale', $locale)
                ->first();

            if ($category) {
                $breadcrumbs[] = [
                    'name' => $category->title,
                    'path' => $currentPath,
                    'type' => 'category',
                    'id' => $category->category_id,
                ];

                continue;
            }

            // Проверяем, является ли сегмент продуктом (обычно последний сегмент)
            $product = DB::table('product_lang')
                ->where('link_rewrite', $segment)
                ->where('locale', $locale)
                ->first();

            if ($product) {
                $breadcrumbs[] = [
                    'name' => $product->name,
                    'path' => $currentPath,
                    'type' => 'product',
                    'id' => $product->product_id,
                ];

                continue;
            }

            // Если последний сегмент "page" с номером, то это пагинация категории
            if ($segment === 'page' && isset($segments[$index + 1]) && is_numeric($segments[$index + 1])) {
                $breadcrumbs[] = [
                    'name' => __('Page', [], $locale).' '.$segments[$index + 1],
                    'path' => $currentPath.'/'.$segments[$index + 1],
                    'type' => 'pagination',
                ];
                // Пропускаем следующий сегмент (номер страницы)
                $index++;

                continue;
            }

            // Обрабатываем известные статические страницы
            $staticPages = [
                'catalog' => __('Catalog', [], $locale),
                'contact' => __('Contact', [], $locale),
                'about' => __('About', [], $locale),
                'search' => __('Search', [], $locale),
                'cart' => __('Cart', [], $locale),
                'account' => __('My Account', [], $locale),
                'orders' => __('Orders', [], $locale),
                'wishlist' => __('Wishlist', [], $locale),
                'login' => __('Login', [], $locale),
                'register' => __('Register', [], $locale),
                // Другие статические страницы...
            ];

            if (isset($staticPages[$segment])) {
                $breadcrumbs[] = [
                    'name' => $staticPages[$segment],
                    'path' => $currentPath,
                    'type' => 'static',
                ];

                continue;
            }

            // Если это не известная статическая страница, категория или продукт, используем форматированный slug
            $breadcrumbs[] = [
                'name' => ucfirst(str_replace('-', ' ', $segment)),
                'path' => $currentPath,
                'type' => 'unknown',
            ];
        }

        return response()->json($breadcrumbs);
    }

    /**
     * Get products in a category for breadcrumbs
     * (Optional if you need to get products with category context)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCategoryContext(Request $request)
    {
        $categoryId = $request->input('category_id');
        $locale = $request->input('locale', app()->getLocale());

        if (! $categoryId) {
            return response()->json(['error' => 'Category ID is required'], 400);
        }

        // Получаем все родительские категории для формирования полного пути хлебных крошек
        $categoryPath = [];
        $currentCategoryId = $categoryId;

        // Ограничитель для предотвращения бесконечного цикла
        $maxDepth = 10;
        $depth = 0;

        while ($currentCategoryId && $depth < $maxDepth) {
            $category = DB::table('categories')
                ->join('category_lang', 'categories.id', '=', 'category_lang.category_id')
                ->where('categories.id', $currentCategoryId)
                ->where('category_lang.locale', $locale)
                ->select(
                    'categories.id',
                    'categories.parent_id',
                    'category_lang.title',
                    'category_lang.link_rewrite'
                )
                ->first();

            if (! $category) {
                break;
            }

            // Добавляем категорию в начало пути
            array_unshift($categoryPath, [
                'id' => $category->id,
                'name' => $category->title,
                'slug' => $category->link_rewrite,
                'path' => "/catalog/{$category->link_rewrite}", // Базовый путь, который можно настроить
            ]);

            $currentCategoryId = $category->parent_id;
            $depth++;
        }

        // Добавляем домашнюю страницу и каталог в начало
        array_unshift($categoryPath, [
            'id' => null,
            'name' => __('Catalog', [], $locale),
            'slug' => 'catalog',
            'path' => '/catalog',
        ]);

        array_unshift($categoryPath, [
            'id' => null,
            'name' => __('Home', [], $locale),
            'slug' => 'home',
            'path' => '/',
        ]);

        return response()->json($categoryPath);
    }
}

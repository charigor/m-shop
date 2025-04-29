<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\CategoryResource;
use App\Http\Resources\Api\ProductResource;
use App\Models\Category;
use App\Models\Product;
use App\Services\Filter\ElasticFilter;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    protected ElasticFilter $filterService;

    public function __construct(ElasticFilter $filterService)
    {
        $this->filterService = $filterService;
    }

    public function index(Request $request, $link_rewrite = null)
    {
        $filters = $request->input('filter', []);
        $locale = app()->getLocale();
        if(!$link_rewrite){
            $start = microtime(true);
            // Передаем null вместо category_id чтобы показать, что нужны все категории
            $facet = $this->filterService->handle(null, $filters);


            $end = microtime(true);
            info('Время выполнения скрипта: ' . round($end - $start, 4) . ' секунд');

            // Получаем продукты с фильтрами, но без ограничения по категории
            $productsQuery = Product::with(['media', 'translate' => function ($query) use ($locale) {
                $query->where('locale', $locale);
            }])
                ->whereHas('translate', fn ($query) => $query->where('locale', $locale));

            // Добавляем фильтр по ID продуктов, если они ограничены фасетным поиском
            if (!empty($facet['productIds'])) {
                $productsQuery->whereIn('id', $facet['productIds']);
            }

            $products = $productsQuery->paginate(25);
            info('=====ProductFromFacet======');
            info(count($facet['productIds']));
            info('=====ProductFromFacet======');
            info('=====ProductQuery======');
            info($productsQuery->count());
            info('=====ProductQuery======');
            return response()->json([
                'category' => null,
                'subcategories' => [], // Можно добавить корневые категории, если нужно
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
        $category = Category::with(['media', 'translate'])
            ->select('categories.*')
            ->leftJoin('category_lang', 'category_lang.category_id', '=', 'categories.id')
            ->where('link_rewrite', $link_rewrite)
            ->first();
        if (! $category) {
            return response()->json([
                'category' => null,
                'subcategories' => [],
                'products' => [],
            ]);
        }

        $subcategories = Category::with(['media', 'translate'])
            ->select('categories.*')
            ->leftJoin('category_lang', 'category_lang.category_id', '=', 'categories.id')
            ->where('locale', $locale)
            ->where('parent_id', $category->id)
            ->get();
        $start = microtime(true);
        $facet = $this->filterService->handle($category->id, $filters);

        $end = microtime(true);
        info('Время выполнения скрипта: ' . round($end - $start, 4) . ' секунд');
        info(count($facet['productIds']));
        $products = Product::with(['media', 'translate' => function ($query) use ($locale) {
            $query->where('locale', $locale);
        }])
            ->whereHas('translate', fn ($query) => $query->where('locale', $locale))
            ->whereIn('id', $facet['productIds'])
            ->paginate(25);

        return response()->json([
            'category' => new CategoryResource($category),
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

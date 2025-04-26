<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\CategoryResource;
use App\Http\Resources\Api\ProductResource;
use App\Models\Category;
use App\Models\Product;
use App\Services\Filter\ElasticFilter;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    protected ElasticFilter $filterService;

    public function __construct(ElasticFilter $filterService)
    {
        $this->filterService = $filterService;
    }
    public function index(Request $request)
    {
        $categories = Category::with(['children', 'translate'])->get()->toTree();
        return response()->json($categories);
    }

    public function show(Request $request, $linkRewrite)
    {
        $data = $request->all();
        $locale = app()->getLocale();
        $category = Category::with(['media', 'translate'])
            ->select('categories.*')
            ->leftJoin('category_lang', 'category_lang.category_id', '=', 'categories.id')
            ->where('link_rewrite', $linkRewrite)
            ->first();
        if (!$category) {
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
        $products = [];

        if ($subcategories->isEmpty()) {
            $res = $this->filterService->handle($category->id,$data);
            $products = Product::with(['media', 'translate' => function ($query) use ($locale) {
                $query->where('locale', $locale);
            }])
                ->whereHas('categories', fn($query) => $query->where('category_id', $category->id))
                ->whereHas('translate', fn($query) => $query->where('locale', $locale))
                ->paginate(24);
        }

        return response()->json([
            'category' => new CategoryResource($category),
            'subcategories' => CategoryResource::collection($subcategories),
            'products' => ProductResource::collection($products),
            'facet' => $res,
            'pagination' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
            ],
        ]);
    }
}

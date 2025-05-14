<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Services\Catalog\CategoryProductUrl;
use App\Services\Filter\ElasticFilter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    protected ElasticFilter $filterService;

    private CategoryProductUrl $urlService;

    public function __construct(ElasticFilter $filterService, CategoryProductUrl $urlService)
    {
        $this->filterService = $filterService;
        $this->urlService = $urlService;
    }

    /**
     * Get categories data
     */
    public function index(Request $request): JsonResponse
    {
        $categories = Category::with(['children', 'translate'])->get()->toTree();

        return response()->json($categories);
    }

    public function exist($link_rewrite): JsonResponse
    {
        return response()->json($this->urlService->isCategorySlug($link_rewrite));
    }

    /**
     * Get localized URL for a category or product
     */
    public function getLocalizedUrl(Request $request): JsonResponse
    {
        $full_path = $request->input('full_path');
        $targetLocale = $request->input('locale');

        if (! $full_path || ! $targetLocale) {
            return response()->json(['error' => 'Missing parameters'], 400);
        }

        $result = $this->urlService->getLocalizedUrl($full_path, $targetLocale);

        if (! $result['success']) {
            return response()->json(['error' => $result['error']], $result['status']);
        }

        return response()->json([
            'localized_slug' => $result['localized_slug'],
            'type' => $result['type'],
            'object_id' => $result['object_id'],
        ]);
    }
}

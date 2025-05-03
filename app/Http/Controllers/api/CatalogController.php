<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Services\Catalog\CatalogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    protected CatalogService $catalogService;

    public function __construct(CatalogService $catalogService)
    {
        $this->catalogService = $catalogService;
    }

    /**
     * Get catalog data
     */
    public function index(Request $request, $link_rewrite = null): JsonResponse
    {
        $start = microtime(true);

        $filters = $request->input('filter', []);
        $locale = app()->getLocale();
        $sort = $request->input('sort', '');

        return $this->catalogService->getCatalog($link_rewrite, $filters, $locale, $sort);
    }
}

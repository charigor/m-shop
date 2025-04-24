<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Services\Contracts\SearchEngineInterface;
use Illuminate\Http\Request;

class SearchController extends Controller
{

    protected SearchEngineInterface $searchService;

    public function __construct(SearchEngineInterface $searchService)
    {
        $this->searchService = $searchService;
    }

    public function search(Request $request): \Illuminate\Http\JsonResponse
    {
        if (!$request->get('q')) {
            return response()->json([]);
        }

        return response()->json(
            $this->searchService->handle($request)
        );
    }
}

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
}

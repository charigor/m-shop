<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $lang = $request->header('Accept-Language', app()->getLocale());
        $categories = Category::with(['children'])->get()->toTree();
        $this->loadTranslationsRecursive($categories, $lang);
        return response()->json($categories);
    }
    private function loadTranslationsRecursive($categories, $lang): void
    {
        foreach ($categories as $category) {
            $category->setRelation('translate', $category->translate($lang)->first());

            if ($category->children->isNotEmpty()) {
                $this->loadTranslationsRecursive($category->children, $lang);
            }
        }
    }
}

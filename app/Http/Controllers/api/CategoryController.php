<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index($slug)
    {

        $category = Category::with(['media'])
            ->selectRaw(
                'categories.*,
                                          category_lang.title,
                                          category_lang.locale,
                                          category_lang.description,
                                          category_lang.meta_description,
                                          category_lang.meta_keywords,
                                          category_lang.meta_title,
                                          category_lang.link_rewrite')
            ->leftJoin('category_lang', 'category_lang.category_id', '=', 'categories.id')
            ->where('locale', app()->getLocale())
            ->where('link_rewrite', $slug)
            ->first();
        if (! $category) {
            return response()->json(['message' => 'Category not found'], 404);
        }
        if (! $category->children || $category->children->isEmpty()) {
            $childCategories = Category::with(['media'])
                ->selectRaw(
                    'categories.*,
                                          category_lang.title,
                                          category_lang.locale,
                                          category_lang.description,
                                          category_lang.meta_description,
                                          category_lang.meta_keywords,
                                          category_lang.meta_title,
                                          category_lang.link_rewrite')
                ->leftJoin('category_lang', 'category_lang.category_id', '=', 'categories.id')
                ->where('locale', app()->getLocale())
                ->where('parent_id', $category->id)
                ->get();

            return response()->json(compact('category', 'childCategories'));
        } else {
            return response()->json( compact('category'));
        }
    }
}

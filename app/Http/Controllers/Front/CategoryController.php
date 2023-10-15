<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function show($slug){

        $category =   Category::with(['media'])
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
                              ->where('locale',app()->getLocale())
                              ->where('link_rewrite',$slug)
                              ->first();

        if(!$category->children)
        {
            $childCategories =   Category::with(['media'])
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
                ->where('locale',app()->getLocale())
                ->where('parent_id',$category->id)
                ->get();
            return  view('front.category.subcategories',compact('category','childCategories'));
        }
        else
        {
            return  view('front.category.products',compact('category'));
        }
    }
}

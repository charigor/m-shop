<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Feature;
use App\Models\FeatureValue;
use App\Models\Product;
use Illuminate\Http\Request;

class SelectController extends Controller
{
    protected $customlists = [
        'categories' => 'categoryList',
        'brands' => 'brandList',
        'bind_products' => 'bindProductList',
        'features' => 'featureList',
        'feature_values' => 'featureValueList',
    ];

    public function load(Request $request)
    {
        if (isset($this->customlists[$request->get('param')])) {
            $method = $this->customlists[$request->get('param')];

            return $this->$method($request->get('term'), $request->get('value'), $request->has('depends') ? $request->get('depends') : null);
        }
    }

    /**
     * @param  null  $value
     * @return array
     */
    private function categoryList()
    {
        return Category::query()->select('categories.id', 'category_lang.title as label')
            ->leftJoin('category_lang', 'categories.id', '=', 'category_lang.category_id')
            ->leftJoin('langs', 'category_lang.locale', '=', 'langs.code')
            ->where('langs.code', app()->getLocale())
            ->orderBy('categories.id')
            ->get()->map(function ($item) {
                return ['value' => $item->id, 'label' => $item->label];
            })
            ->toArray();
    }

    /**
     * @return mixed[]
     */
    private function brandList()
    {
        return Brand::query()->select('brands.id', 'brands.name as label')
            ->orderBy('brands.name')
            ->get()->map(function ($item) {
                return ['value' => $item->id, 'label' => $item->label];
            })
            ->toArray();
    }

    /**
     * @return mixed[]
     */
    private function featureList()
    {
        return Feature::query()->select('features.id', 'feature_lang.name as label')
            ->leftJoin('feature_lang', 'features.id', '=', 'feature_lang.feature_id')
            ->leftJoin('langs', 'feature_lang.locale', '=', 'langs.code')
            ->where('langs.code', app()->getLocale())
            ->orderBy('features.id')
            ->get()->map(function ($item) {
                return ['value' => $item->id, 'label' => $item->label];
            })
            ->toArray();
    }

    private function featureValueList($term, $value, $depends)
    {
        if ($depends) {
            return FeatureValue::query()->select('feature_values.id', 'feature_value_lang.value as label,feature_values.feature_id')
                ->leftJoin('feature_value_lang', 'feature_values.id', '=', 'feature_value_lang.feature_value_id')
                ->leftJoin('langs', 'feature_value_lang.locale', '=', 'langs.code')
                ->where('langs.code', app()->getLocale())
                ->when($depends, function ($query) use ($depends) {
                    $query->where('feature_values.feature_id', '=', $depends);
                })
                ->orderBy('feature_values.id')
                ->get()->map(function ($item) {
                    return ['value' => $item->id, 'label' => $item->label];
                })
                ->toArray();
        } else {
            return [];
        }
    }

    private function bindProductList($term)
    {
        if ($term) {
            return Product::query()->select('products.id', 'product_lang.name as label')
                ->leftJoin('product_lang', 'products.id', '=', 'product_lang.product_id')
                ->leftJoin('langs', 'product_lang.locale', '=', 'langs.code')
                ->where('langs.code', app()->getLocale())
                ->where('product_lang.name', 'LIKE', '%'.$term.'%')
                ->orderBy('products.id')
                ->limit(3)
                ->get()
                ->map(function ($item) {
                    return ['value' => $item->id, 'label' => $item->label];
                })
                ->toArray();
        } else {
            return [];
        }

    }
}

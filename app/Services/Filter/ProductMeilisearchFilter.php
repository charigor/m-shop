<?php

namespace App\Services\Filter;

use App\Models\CategoryLang;
use App\Models\Feature;
use App\Models\FeatureValueLang;
use App\Models\Product;
use Illuminate\Support\Arr;
use Meilisearch\Endpoints\Indexes;

class ProductMeilisearchFilter implements ProductFilterContract
{
    public array $params;

    public string $cost;

    public string $sortBy;

    public string $contextID;

    public string $context;

    public int $perPage;

    public const MULTIPLE = 100;

    public function __construct() {}

    /**
     * @return $this
     */
    public function build($params)
    {
        $this->params = Arr::except($params, ['cost', 'contextID', 'context', 'sort', 'perPage']);
        $this->cost = implode(',', Arr::only($params, 'cost'));
        $this->context = implode(',', Arr::only($params, 'context'));
        $this->contextID = implode(',', Arr::only($params, 'contextID'));
        $this->sortBy = implode(',', Arr::only($params, 'sort'));
        $this->perPage = implode(',', Arr::only($params, 'perPage'));

        return $this;
    }

    public function search(): mixed
    {

        return $this->buildDocument();
    }

    public function prepareFacet(): array
    {

        $facets = array_merge($this->buildDocument()['facetDistribution'], $this->buildFacets());

        return collect($this->modifyFacet($this->resetFacet($facets, $this->buildAllFacets())))->map(fn ($q) => collect($q)->sortBy('value')->toArray())->toArray();
    }

    public function index(): array
    {
        // TODO: Implement index() method.
        return [];
    }

    public function buildFacets(): array
    {
        return collect($this->params)->map(function ($value, $k) {
            return Product::search('', function (Indexes $meiliSearch, $query, $options) use ($k) {
                return $meiliSearch->search($query, $this->makeQueryString($options, $k));
            })->raw()['facetDistribution'];
        })->mapWithKeys(function ($a) {
            return $a;
        })->toArray();
    }

    public function buildAllFacets(): array
    {
        return Product::search('', function (Indexes $meiliSearch, $query, $options) {
            $options['facets'] = $this->context === 'brand' ? ['feature', 'category'] : ['feature', 'brand'];

            return $meiliSearch->search($query, $options);
        })->raw()['facetDistribution'];
    }

    public function buildDocument(): mixed
    {
        return Product::search('', function (Indexes $meiliSearch, $query, $options) {
            return $meiliSearch->search($query, $this->makeQueryString($options));
        })->raw();
    }

    private function makeQueryString($options, $name = null): array
    {
        $facets = $this->context === 'brand' ? ['feature', 'category'] : ['feature', 'brand'];
        $options['sort'] = [$this->sortBy, 'quantity:desc'];
        $options['facets'] = $name ? [($name != 'brand' && $name != 'category') ? 'feature.'.$name : $name] : $facets;

        $filters = collect($this->params)->filter(fn ($filter, $key) => (! empty($filter) && (! $key || $key !== $name)))->recursive()->map(function ($value, $key) {

            if ($key !== 'brand' && $key != 'category') {
                $key = 'feature.'.$key;
            }

            return $value->map(fn ($value) => $key.'="'.$value.'"'
            )->join(' OR ');
        })->flatten()->join(' ) AND (');

        $filters = $filters ? '('.$filters.')' : '';
        if ($this->cost) {
            $filters .= (! $filters ? ' ' : ' AND ').' cost <= '.($this->cost * self::MULTIPLE);
        }
        if ($this->context === 'category' && $this->contextID) {
            $filters .= (! $filters ? ' ' : ' AND ').' category = '.$this->contextID;
        }

        if ($this->context === 'brand' && $this->contextID) {
            $filters .= (! $filters ? ' ' : ' AND ')." brand = '$this->contextID'";
        }
        $options['filter'] = $filters;

        return $options;
    }

    //    /**
    //     * @param $basic
    //     * @param $facets
    //     * @return mixed
    //     */
    //    private function mergeFacets($basic, $facets)?
    //    {
    //        foreach ($facets as $key => $value) {
    //            $basic[$key] = $value;
    //        }
    //        return $basic;
    //    }

    public function modifyFilterKeys($facet, $substr): array
    {

        unset($facet[$substr]);

        foreach ($facet as $key => $item) {
            if (str_contains($key, 'feature.')) {
                $newKey = preg_replace('$'.$substr.'.$', '', $key);

                $facet[$newKey] = $item;
                unset($facet[$key]);
            }
        }

        return $facet;
    }

    public function resetFacet($facet, $allFacets): array
    {
        foreach ($allFacets as $key => $value) {

            foreach ($value as $k => $inner) {

                if ($key == 'brand' || $key == 'category') {

                    if (! isset($facet[$key][$k])) {
                        $count = $allFacets[$key][$k] = 0;
                        $facet[$key][$k] = $count;

                    }

                } else {

                    if (! isset($facet[$key][$k])) {
                        $count = $allFacets['feature.'.$key][$k] = 0;
                        $facet[$key][$k] = $count;
                    }
                }

            }

        }

        return $facet;
    }

    public function modifyFacet($facet): array
    {

        $facet = $this->modifyFilterKeys($facet, 'feature');
        FeatureValueLang::query()
            ->selectRaw('feature_values.id,value,feature_values.feature_id,guard_name,name')
            ->leftJoin('feature_values', 'feature_values.id', '=', 'feature_value_lang.feature_value_id')
            ->leftJoin('features', 'features.id', '=', 'feature_values.feature_id')
            ->leftJoin('feature_lang', 'feature_lang.feature_id', '=', 'feature_values.feature_id')
            ->where('feature_value_lang.locale', app()->getLocale())
            ->where('feature_lang.locale', app()->getLocale())
            ->get()
            ->groupBy('guard_name')
            ->each(function ($i, $key) use (&$facet) {
                $i->each(function ($item, $k) use (&$facet, $key) {
                    if (isset($facet[$key])) {
                        if (isset($facet[$key][$item->id])) {
                            $count = $facet[$key][$item->id];
                            unset($facet[$key][$item->id]);
                            $facet[$key][$k] = [
                                'id' => $item->id,
                                'value' => $item->value,
                                'name' => $item->name,
                                'count' => $count,
                                'guard_name' => $item->guard_name,
                            ];
                        }
                    }
                });
            });

        Feature::query()
            ->selectRaw('guard_name,name')
            ->leftJoin('feature_lang', 'feature_lang.feature_id', '=', 'features.id')
            ->where('feature_lang.locale', app()->getLocale())
            ->get()
            ->each(function ($item) use (&$facet) {
                if (isset($facet[$item->guard_name])) {
                    $res = $facet[$item->guard_name];
                    unset($facet[$item->guard_name]);
                    $facet[$item->name] = $res;
                }
            });

        if (isset($facet['category'])) {
            CategoryLang::query()->with('category')
                ->where('locale', app()->getLocale())->get(['category_id', 'title', 'link_rewrite'])->each(function ($item, $key) use (&$facet) {
                    if (isset($facet['category'])) {
                        if (isset($facet['category'][$item->category_id])) {
                            $count = $facet['category'][$item->category_id];
                            unset($facet['category'][$item->category_id]);
                            $facet[trans('page/category.title_plural')][$key] = [
                                'id' => $item->category_id,
                                'value' => $item->link_rewrite,
                                'name' => trans('page/category.title_plural'),
                                'count' => $count,
                                'guard_name' => 'category',
                            ];
                        }
                    }
                });
            unset($facet['category']);
        }

        if (isset($facet['brand'])) {
            collect($facet['brand'])->each(function ($item, $key) use (&$facet) {
                $count = $facet['brand'][$key];
                $facet[trans('page/brand.title_plural')][] = ['count' => $count, 'value' => $key, 'name' => trans('page/brand.title_plural'), 'guard_name' => 'brand'];
            });
            unset($facet['brand']);
        }

        return collect($facet)->each(function ($value) {
            collect($value)->sortBy('value');
        })->toArray();
    }
}

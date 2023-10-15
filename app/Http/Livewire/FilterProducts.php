<?php

namespace App\Http\Livewire;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Feature;
use App\Models\Product;
use App\Services\Filter\ProductFilterContract;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Livewire\Component;
class FilterProducts extends Component
{


    public string $sortBy = 'price:asc';
    public array $price = ['max' => null, 'min' => null], $filter = [];
    protected $queryString = ['filter', 'sortBy'];
    public $listeners = ['cartAddedOrUpdated', 'increase' => 'addQuantity', 'decrease' => 'removeQuantity'];
    protected \App\Services\Cart\Cart $cartService;
    protected ProductFilterContract $facetFilter;
    public Category|Brand $model;
    public string $pageTitle;


    /**
     * @param \App\Services\Cart\Cart $cart
     * @param ProductFilterContract $facetFilter
     * @return void
     */
    public function boot(\App\Services\Cart\Cart $cart, ProductFilterContract $facetFilter): void
    {
        $this->facetFilter = $facetFilter;
        $this->cartService = $cart;
    }

    /**
     * @param $model
     * @return void
     */
    public function mount($model = null): void
    {
        $this->model = $model;
        $this->pageTitle = $model ? ($this->model::MODEL_NAME == 'category' ? $this->model->title : $this->model->name) : '';
        if (!$model) $this->filter = $this->filter ?? array_merge(Feature::query()->has('products')->get('guard_name')->groupBy('guard_name')->keys()->mapWithKeys(fn($key) => [$key => []])->toArray(), ['category' => [], 'brand' => [], 'price' => []]);
        if ($model->getTable() === 'categories') $this->filter = $this->filter ?? array_merge(Feature::query()->whereHas('products.categories', fn($query) => $query->where('id', $this->model->id))->get('guard_name')->groupBy('guard_name')->keys()->mapWithKeys(fn($key) => [$key => []])->toArray(), ['brand' => [], 'price' => []]);
        if ($model->getTable() === 'brands') $this->filter = $this->filter ?? array_merge(Feature::query()->whereHas('products.brand', fn($query) => $query->where('id', $this->model->id))->get('guard_name')->groupBy('guard_name')->keys()->mapWithKeys(fn($key) => [$key => []])->toArray(), ['category' => [], 'price' => []]);
    }

    /**
     * @param $name
     * @param $value
     * @return void
     */
    public function updated($name, $value): void
    {
        $q = explode('.', $name);
        if ($value === false && isset($this->filter[$q[1]][$q[2]])) {
            unset($this->filter[$q[1]][$q[2]]);
            if (empty($this->filter[$q[1]])) {
                unset($this->filter[$q[1]]);
                $this->filter[$q[1]] = [];
            }
        }
    }

    /**
     * @param $key
     * @return void
     */
    public function clearGroupFilter($key): void
    {
        unset($this->filter[$key]);

    }

    /**
     * @param $key
     * @param $name
     * @return void
     */
    public function clearElementFilter($key, $name): void
    {
        unset($this->filter[$key][$name]);
        if (empty($this->filter[$key])) unset($this->filter[$key]);
    }

    /**
     * @return void
     */
    public function clearAllFilters(): void
    {
        $this->filter = [];
        $this->price['max'] = null;
    }

    /**
     * @return void
     */
    public function cartAddedOrUpdated(): void
    {
        $this->cartService->get();
    }

    /**
     * @param int $productID
     * @return void
     */
    public function addToCart(int $productID): void
    {
        if ($product = Product::where('id', $productID)->active()->first()) {
            if ($product->quantity > 0) {
                $this->cartService->add([
                    'product_id' => $product->id,
                    'name' => $product->translate->name,
                    'image' => $product->getFirstMediaUrl('image'),
                    'price' => $product->price,
                    'quantity' => 1,
                ]);
                $this->emit('cartAddedOrUpdated');
            }
        }
    }

    /**
     * @param int $productID
     * @return void
     */
    public function addQuantity(int $productID): void
    {
        if ($product = Product::where('id', $productID)->active()->first()) {
            if ($product->quantity > 1) {
                $this->cartService->addQuantity($productID);
            }
            $this->emit('cartAddedOrUpdated');
        }
    }

    /**
     * @param int $productID
     * @return void
     */

    public function removeQuantity(int $productID): void
    {
        if ($product = Product::where('id', $productID)->active()->first()) {
            if ($product->quantity > 1) {
                $this->cartService->removeQuantity($productID);
            }
            $this->emit('cartAddedOrUpdated');
        }
    }

    /**
     * @param Request $request
     * @return View|Application|Factory|\Illuminate\Contracts\Foundation\Application
     */
    public function render(Request $request): View|Application|Factory|\Illuminate\Contracts\Foundation\Application
    {
        $meili = $this->querySearchWithFilter($this->filter);

        if (!$this->model) $maxPrice = priceFormat(Product::active()->max('price'), 0);
        if ($this->model->getTable() === 'categories') $maxPrice = priceFormat(Product::when($this->model->id, fn($q) => $q->whereHas($this->model->getTable(), fn($q) => $q->where('id', $this->model->id)))->active()->max('price'), 0);
        if ($this->model->getTable() === 'brands') $maxPrice = priceFormat(Product::where('brand_id', $this->model->id)->active()->max('price'), 0);
        //        $this->price['min'] = $this->filter['price'][0] ?? priceFormat(Product::when($this->categoryId, fn($q) => $q->whereHas('categories', fn($q) => $q->where('id', $this->categoryId)))->active()->min('price'));
        $this->price['max'] = $this->price['max'] ?? $maxPrice;

        $filterNav = $this->getFilterNavigation($meili['facet']);
        $filterTranslation = $this->getFilterTranslation($meili['facet']);

        return view('livewire.filter-products', [
                'facet' => $meili['facet'],
                'hits' => $meili['documents'],
                'price',
                'maxPrice' => $maxPrice,
                'filterNav' => $filterNav,
                'filterTrans' => $filterTranslation,
                'title'
            ]
        );
    }

    /**
     * @param $facet
     * @return Collection
     */
    public function getFilterNavigation($facet) :Collection
    {
        return collect($facet)
            ->flatten(1)
            ->groupBy('guard_name')
            ->map(fn($value, $key) => collect($value)->filter(fn($v) => (isset($this->filter[$key][$v['value']]))));
    }

    /**
     * @param $facet
     * @return array
     */
    public function getFilterTranslation($facet) :Collection
    {
        return collect($facet)->flatten(1)->groupBy('guard_name')->keys()->combine(collect($facet)->flatten(1)->groupBy('name')->keys());
    }


    /**
     * @param $options
     * @param $data
     * @return array
     */
    public function filterByPrice($options, $data): array
    {
        $options['filter'][] = 'price >=' . $data['priceMin'];
        $options['filter'][] = 'price <=' . $data['priceMax'];
        return $options;
    }

    /**
     * @param $data
     * @return array
     */
    public function querySearchWithFilter($data): array
    {
        $data['sort'] = $this->sortBy;
        $data['price'] = $this->price['max'];
        $data['contextID'] = $this->model ? ($this->model::MODEL_NAME === 'brand' ? $this->model->name : $this->model->id) : null;
        $data['context'] = $this->model ? $this->model::MODEL_NAME : null;
        $hits = $this->facetFilter->build($data)->search();
        $facet = $this->facetFilter->prepareFacet();
        $sort = explode(':', $this->sortBy);
        return [
            'documents' => Product::whereIn('id', collect($hits['hits'])->pluck('id'))->orderBy($sort[0], $sort[1])->paginate(20),
            'facet' => $facet,
        ];
    }


}

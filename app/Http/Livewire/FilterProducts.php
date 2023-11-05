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
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class FilterProducts extends Component
{
    use WithPagination;

    public string $sortBy = 'cost:asc';

    public array $cost = ['max' => null, 'min' => null];

    public array $filter = [];

    protected int $perPage = 5;

    protected $queryString = ['filter', 'sortBy'];

    public $listeners = ['cartAddedOrUpdated', 'increase' => 'addQuantity', 'decrease' => 'removeQuantity'];

    protected \App\Services\Cart\Cart $cartService;

    protected ProductFilterContract $facetFilter;

    public Category|Brand $model;

    public string $pageTitle;

    public function boot(\App\Services\Cart\Cart $cart, ProductFilterContract $facetFilter): void
    {
        $this->facetFilter = $facetFilter;
        $this->cartService = $cart;
    }

    public function mount($model = null): void
    {
        $this->model = $model;
        $this->pageTitle = $model ? ($this->model::MODEL_NAME == 'category' ? $this->model->title : $this->model->name) : '';
        if (! $model) {
            $this->filter = $this->filter ?? array_merge(Feature::query()->has('products')->get('guard_name')->groupBy('guard_name')->keys()->mapWithKeys(fn ($key) => [$key => []])->toArray(), ['category' => [], 'brand' => [], 'cost' => []]);
        }
        if ($model->getTable() === 'categories') {
            $this->filter = $this->filter ?? array_merge(Feature::query()->whereHas('products.categories', fn ($query) => $query->where('id', $this->model->id))->get('guard_name')->groupBy('guard_name')->keys()->mapWithKeys(fn ($key) => [$key => []])->toArray(), ['brand' => [], 'cost' => []]);
        }
        if ($model->getTable() === 'brands') {
            $this->filter = $this->filter ?? array_merge(Feature::query()->whereHas('products.brand', fn ($query) => $query->where('id', $this->model->id))->get('guard_name')->groupBy('guard_name')->keys()->mapWithKeys(fn ($key) => [$key => []])->toArray(), ['category' => [], 'cost' => []]);
        }
    }

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

    public function clearGroupFilter($key): void
    {
        unset($this->filter[$key]);

    }

    public function clearElementFilter($key, $name): void
    {
        unset($this->filter[$key][$name]);
        if (empty($this->filter[$key])) {
            unset($this->filter[$key]);
        }
    }

    public function clearAllFilters(): void
    {
        $this->filter = [];
        $this->cost['max'] = null;
    }

    public function cartAddedOrUpdated(): void
    {
        $this->cartService->get();
    }

    public function addToCart(int $productID): void
    {
        if ($product = Product::where('id', $productID)->active()->first()) {
            if ($product->quantity > 0) {
                $this->cartService->add([
                    'product_id' => $product->id,
                    'name' => $product->translate->name,
                    'image' => $product->getFirstMediaUrl('image'),
                    'cost' => $product->cost,
                    'quantity' => 1,
                ]);
                $this->emit('cartAddedOrUpdated');
            }
        }
    }

    public function addQuantity(int $productID): void
    {
        if ($product = Product::where('id', $productID)->active()->first()) {
            if ($product->quantity > 1) {
                $this->cartService->addQuantity($productID);
            }
            $this->emit('cartAddedOrUpdated');
        }
    }

    public function removeQuantity(int $productID): void
    {
        if ($product = Product::where('id', $productID)->active()->first()) {
            if ($product->quantity > 1) {
                $this->cartService->removeQuantity($productID);
            }
            $this->emit('cartAddedOrUpdated');
        }
    }

    public function render(Request $request): View|Application|Factory|\Illuminate\Contracts\Foundation\Application
    {
        $meili = $this->querySearchWithFilter($this->filter);

        if (! $this->model) {
            $maxPrice = priceFormat(Product::active()->max('cost'), 0);
        }
        if ($this->model->getTable() === 'categories') {
            $maxPrice = priceFormat(Product::when($this->model->id, fn ($q) => $q->whereHas($this->model->getTable(), fn ($q) => $q->where('id', $this->model->id)))->active()->max('cost'), 0);
        }
        if ($this->model->getTable() === 'brands') {
            $maxPrice = priceFormat(Product::where('brand_id', $this->model->id)->active()->max('cost'), 0);
        }
        //        $this->price['min'] = $this->filter['price'][0] ?? priceFormat(Product::when($this->categoryId, fn($q) => $q->whereHas('categories', fn($q) => $q->where('id', $this->categoryId)))->active()->min('price'));
        $this->cost['max'] = $this->cost['max'] ?? $maxPrice;

        $filterNav = $this->getFilterNavigation($meili['facet']);
        $filterTranslation = $this->getFilterTranslation($meili['facet']);

        return view('livewire.filter-products', [
            'facet' => $meili['facet'],
            'hits' => $meili['documents'],
            'cost',
            'maxPrice' => $maxPrice,
            'filterNav' => $filterNav,
            'filterTrans' => $filterTranslation,
            'title',
        ]
        );
    }

    public function getFilterNavigation($facet): Collection
    {
        return collect($facet)
            ->flatten(1)
            ->groupBy('guard_name')
            ->map(fn ($value, $key) => collect($value)->filter(fn ($v) => (isset($this->filter[$key][$v['value']]))));
    }

    public function getFilterTranslation($facet): Collection
    {
        return collect($facet)->flatten(1)->groupBy('guard_name')->keys()->combine(collect($facet)->flatten(1)->groupBy('name')->keys());
    }

    public function filterByPrice($options, $data): array
    {
        $options['filter'][] = 'cost >='.$data['priceMin'];
        $options['filter'][] = 'cost <='.$data['priceMax'];

        return $options;
    }

    public function querySearchWithFilter($data): array
    {
        $data['sort'] = $this->sortBy;
        $data['cost'] = $this->cost['max'];
        $data['perPage'] = $this->perPage;
        $data['contextID'] = $this->model ? ($this->model::MODEL_NAME === 'brand' ? $this->model->name : $this->model->id) : null;
        $data['context'] = $this->model ? $this->model::MODEL_NAME : null;
        $hits = $this->facetFilter->build($data)->search();
        $facet = $this->facetFilter->prepareFacet();

        $sort = explode(':', $this->sortBy);

        return [
            'documents' => Product::with('attributes.attributes.group.translate', 'attributes.attributes.translate')->whereIn('id', collect($hits['hits'])->pluck('id'))->select(DB::raw('CASE WHEN quantity > 0 THEN 1 ELSE 0 END AS priority'), 'products.*')->orderByDesc('priority')->orderBy($sort[0], $sort[1])->paginate($this->perPage),
            'facet' => $facet,
        ];
    }
}

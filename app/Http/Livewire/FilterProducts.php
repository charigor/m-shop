<?php

namespace App\Http\Livewire;

use App\Models\Brand;
use App\Models\Feature;
use App\Models\FeatureValue;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;

class FilterProducts extends Component
{
    public Collection $products;
    public string $sortBy = 'name';
    public string $category_id = '';
    public array $price = [],$filter = ['brand' => [], 'price' => [], 'feature' => []];
    protected string $lastFilter = '', $prevFilter = '';

    protected $queryString = ['category_id' => ['except' => '', 'as' => 'cat_id'], 'filter' => ['except' => '', 'as' => 'f']];
    public $listeners = ['cartAddedOrUpdated', 'increase' => 'addQuantity', 'decrease' => 'removeQuantity'];
    protected \App\Services\Cart\Cart $cartService;

    public function boot(\App\Services\Cart\Cart $cart): void
    {
        $this->cartService = $cart;
    }

    public function mount($category_id): void
    {

        $this->category_id = $category_id;
        $this->price[0] = $this->filter['price'][0] ?? priceFormat(Product::when($this->category_id, fn($q) => $q->whereHas('categories', fn($q) => $q->where('category_id', $this->category_id)))->active()->min('price'));
        $this->price[1] = $this->filter['price'][1] ?? priceFormat(Product::when($this->category_id, fn($q) => $q->whereHas('categories', fn($q) => $q->where('category_id', $this->category_id)))->active()->max('price'));

    }

    public function updating($name, $value)
    {
        if ($name === 'filter.brand') {
            session(['lastFilter' => 'brand']);
            $this->lastFilter = $value ? 'brand' : null;
        }
        if ($name === 'filter.price') {
            session(['lastFilter' => 'price']);
            $this->lastFilter = $value ? 'price' : null;
        }
        if ($name === 'filter.feature') {
            session(['lastFilter' => $value]);
            $this->lastFilter = $value ? 'feature_' . FeatureValue::where('id', $value)->first()->feature_id : null;
        }
    }

    public function updatePrice(): void
    {
        session(['lastFilter' => 'price']);
        $this->filter['price'] = $this->price;
        $this->lastFilter = 'price';

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
                    'price' => $product->price,
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

    public function render(): View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {

        if ($value = session()->get('lastFilter')) {
            if (($value !== 'brand' && $value !== 'price')) {
                $this->lastFilter = $value ? 'feature_' . FeatureValue::where('id', $value)->first()->feature_id : null;
            } else {
                $this->lastFilter = $value;
            }
        }
        $this->products = Product::query()->selectRaw(
            'products.*,
             product_lang.name,
             product_lang.locale,
             product_lang.product_id,
             product_lang.description,
             product_lang.meta_keywords,
             product_lang.meta_description')
            ->leftJoin('product_lang', 'product_lang.product_id', '=', 'products.id')
            ->active()
            ->when($this->category_id, fn($q) => $q->whereHas('categories', fn($q) => $q->where('category_id', $this->category_id)))
            ->when(isset($this->filter['brand']), function ($q) {
                $q->when((count($this->filter['brand'])), fn($q) => $q->whereHas('brand', fn($q) => $q->whereIn('brand_id', $this->filter['brand'])));
            })
            ->when(isset($this->filter['price']), function ($q) {
                $q->when(($this->filter['price'] && count($this->filter['price'])), fn($q) => $q->whereBetween('price', $this->filter['price']));
            })
            ->when(isset($this->filter['feature']), function ($q) {
                $q->when(($this->filter['feature']), function ($q) {
                    $q->whereHas('featureValues', fn($q) => $q->whereIn('id', $this->filter['feature']));
                });
            })
            ->when(($this->sortBy && $this->sortBy === 'cheap'), fn($q) => $q->orderBy('price', 'asc'))
            ->when(($this->sortBy && $this->sortBy === 'expensive'), fn($q) => $q->orderBy('price', 'desc'))
            ->when(($this->sortBy && $this->sortBy === 'name'), fn($q) => $q->orderBy('name', 'asc'))
            ->where('locale', app()->getLocale())->groupBy('products.id')->get();
        $ids = $this->products->pluck('id');
        $brands = Brand::active()
            ->withCount([
                    'products' => fn($q) => $q->whereHas('categories', fn($q) => $q->where('category_id', $this->category_id))
                        ->when(($this->lastFilter !== 'brand' && $this->prevFilter !== 'brand'), function ($q) use ($ids) {
                            $q->whereIn('id', $ids);
                        })
                        ->when((!empty($this->filter['price'])), function ($q) {
                            $q->whereBetween('price', $this->filter['price']);
                        })
                        ->when(isset($this->filter['feature']), function ($q) {
                            $q->when($this->filter['feature'], function ($q) {
                                $q->whereHas('featureValues', fn($q) => $q->whereIn('id', $this->filter['feature']));
                            });
                        })
                ]
            )
            ->get();
        $features = Feature::whereHas('products', fn($q) => $q->whereHas('categories', fn($q) => $q->where('category_id', $this->category_id)))
            ->with('featureValue', fn($q) => $q->withCount(['featureValuesProduct' =>
                function ($q) use ($ids) {
                    $q->when((strpos($this->lastFilter, "feature") !== false && (strpos($this->prevFilter, "feature") !== false || !$this->prevFilter)),
                        function ($q) use ($ids) {
                            $q->when(((($this->lastFilter != $this->prevFilter) && $this->prevFilter === null) || ($this->prevFilter === $this->lastFilter)), function ($q) use ($ids) {
                                $pieces = explode("_", $this->lastFilter);
                                $q->when(isset($pieces[1]), function ($q) use ($pieces, $ids) {
                                    $q->where(function ($q) use ($pieces, $ids) {
                                        $q->whereIn('product_id', $ids)->orWhere('feature_id', $pieces[1]);
                                    });
                                });

                            });
                        })
                        ->when((!empty($this->filter['brand'])), function ($q) {
                            $q->whereIn('brand_id', $this->filter['brand']);
                        })
                        ->when((!empty($this->filter['price'])), function ($q) {
                            $q->whereBetween('price', $this->filter['price']);
                        });
                }]))
            ->get();

        $this->prevFilter = $this->lastFilter;


        $allFilters = $this->getAllFilters();
        return view('livewire.filter-products', ['products' => $this->products, 'brands' => $brands, 'features' => $features,'allFilters' => $allFilters]);
    }
    public function getAllFilters(): array
    {
        $result = [];

        foreach($this->filter as $key => $item){
            if($key === 'brand'){
              $result['brand'] =  Brand::whereIn('id',$item)->get()->map(function($item) { return json_decode(json_encode(['id' => $item->id,'name' => $item->name]));});
            }
            if($key === 'price'){
                $result['price'] =  json_decode(json_encode($this->filter['price']));
            }
            else{
                $result['feature'] =  FeatureValue::whereIn('id',$item)->with('feature')->get()->map(function($item) { return json_decode(json_encode(['feature_value_id' => $item->id,'guard_name' => $item->feature->guard_name,'name' => $item->feature->translate->name,'value_name' => $item->translate->value]));})->groupBy('guard_name');
            }
        }
        return $result;
    }
}

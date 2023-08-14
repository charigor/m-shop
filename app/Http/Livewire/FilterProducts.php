<?php

namespace App\Http\Livewire;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Feature;
use App\Models\FeatureProduct;
use App\Models\Product;
use App\Services\Contracts\CartInterface;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use function ZipStream\Test\__toString;
use Illuminate\Support\Facades\Cookie;
use Barryvdh\Debugbar;

class FilterProducts extends Component
{

    public $products,$builderProduct,$category_id,$brands_id = [],$filter = [],$sortBy = 'name';

    protected $queryString = ['category_id' => ['except' => '', 'as' => 'cat_id'],'filter' => ['except' => '', 'as' => 'f']];
    public $listeners = ['cartAddedOrUpdated','increase' => 'addQuantity','decrease' => 'removeQuantity'];
    protected \App\Services\Cart\Cart $cartService;
    public function boot(\App\Services\Cart\Cart $cart)
    {
        $this->cartService = $cart;
    }
    public function mount($category_id)
    {

        $this->category_id =  $category_id;

    }
    public function cartAddedOrUpdated(){

        $this->cartService->get();
    }

    public function addToCart(int $productID){
        if($product = Product::where('id',$productID)->active()->first()){
            if($product->quantity > 0){
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
    public function addQuantity(int $productID){
        if($product = Product::where('id',$productID)->active()->first()) {
            if ($product->quantity > 1) {
                $this->cartService->addQuantity($productID);
            }
            $this->emit('cartAddedOrUpdated');
        }
    }
    public function removeQuantity(int $productID){
        if($product = Product::where('id',$productID)->active()->first()) {
            if ($product->quantity > 1) {
                $this->cartService->removeQuantity($productID);
            }
            $this->emit('cartAddedOrUpdated');
        }
    }
    public function render()
    {
        $builder = FeatureProduct::query();
        foreach($this->filter as $k => $item){
            $elem[$k] = json_decode($item,true);
            foreach($elem[$k] as $key => $value){
                $fakeBuilder = clone $builder;
                if($key === 'brand'){
                    $checker =  $fakeBuilder->leftJoin('products','products.id','=','feature_product.product_id')->where('products.brand_id',$value)->first();
                }else{
                    $checker = $fakeBuilder->where(['feature_id' => $key ,'feature_value_id' => $value])->first();
                }

                if($checker){
                    if($key === 'brand'){
                        $builder->leftJoin('products','products.id','=','feature_product.product_id')->where('products.brand_id',$value);
                    }else{
                        $builder->where(['feature_id' => $key ,'feature_value_id' => $value]);
                    }

                }

                else{
                    break;
                }

            }
        }
        $ids = $builder->get()->unique('product_id')->pluck('product_id');
        $builderProduct = Product::query()->selectRaw(
            'products.*,
             product_lang.name,
             product_lang.locale,
             product_lang.product_id,
             product_lang.description,
             product_lang.meta_keywords,
             product_lang.meta_description')
            ->leftJoin('product_lang', 'product_lang.product_id', '=', 'products.id')
            ->active()

//            ->when($this->brands_id,fn($q) => $q->whereHas('brand', fn($q) => $q->whereIn('brand_id',$this->brands_id)))
            ->when($this->filter,function($q) use($ids) { $q->whereIn('products.id',$ids);})
            ->when($this->category_id,fn($q) =>  $q->whereHas('categories', fn($q) => $q->where('category_id',$this->category_id)))
            ->when(($this->sortBy && $this->sortBy === 'cheap'),fn($q) => $q->orderBy('price','asc'))
            ->when(($this->sortBy && $this->sortBy === 'expensive'),fn($q) => $q->orderBy('price','desc'))
            ->when(($this->sortBy && $this->sortBy === 'name'),fn($q) => $q->orderBy('name','asc'))
            ->where('locale',app()->getLocale())->groupBy('products.id');

        $this->products =  $builderProduct->get();


        $features = Feature::whereHas('products.categories',fn($q) => $q->where('category_id', $this->category_id))
                            ->with([
                                'translation',
                                'featureValue.translation',
                                'featureValue.featureValuesProduct' => fn($q) => $q->when($this->filter,function($q) use($ids) {$q->whereIn('product_id',$ids);})
                                    ->whereHas('categories',fn($q) => $q->where('category_id', $this->category_id))])
                            ->get()
                            ->map(function($item) {
                                $item->trans = $item->translation->where('locale',app()->getLocale())->first();
                                $item->featureValue =  $item->featureValue->map(function($item) {
                                $item->trans = $item->translation->where('locale',app()->getLocale())->first(); return $item;});
                               return $item;
                            });

        $brands = Brand::active()
                        ->withCount(['products' => fn($q) => $q->whereHas('categories',fn($q) => $q->where('category_id', $this->category_id))
                        ->when($this->filter,function($q) use($ids) { $q->whereIn('products.id',$ids);})])
                        ->get();
        return view('livewire.filter-products',['products' => $this->products,'brands' => $brands,'features' => $features]);
    }
}

<?php

namespace App\Http\Livewire;

use App\Models\Attribute;
use App\Models\AttributeProduct;
use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ProductCard extends Component
{
    public \App\Models\Product $product;
    public array $attributes;
    public array $attr = [];
    protected $queryString = ['attr'];
    public  $productAttribute;
    public $listeners = ['cartAddedOrUpdated', 'increase' => 'addQuantity', 'decrease' => 'removeQuantity'];
    protected \App\Services\Cart\Cart $cartService;

    /**
     * @param \App\Services\Cart\Cart $cart
     * @return void
     */
    public function boot(\App\Services\Cart\Cart $cart): void
    {
        $this->cartService = $cart;
    }
    /**
     * @param \App\Models\Product $product
     * @return void
     */
    public function mount(\App\Models\Product $product): void
    {
        $this->product = $product;
    }
    /**
     * @param int $productID
     * @param int|null $attributeID
     * @return void
     */
    public function addToCart(int $productID, int|null $attributeID = null): void
    {
        if($attributeID) {
            $attributeName = AttributeProduct::where('id', $attributeID)->first()->attributes()->first()->translate->name;
            if ($product = Product::where('id', $productID)->whereHas('attributes', fn($q) => $q->where('id', $attributeID))->active()->first()) {
                if ($product->attributes()->where('id', $attributeID)->first()->quantity > 0) {
                    $this->cartService->add([
                        'product_id' => $product->id,
                        'attribute_id' => $attributeID,
                        'name' => $product->translate->name . ' ' . $attributeName,
                        'image' => $product->attributes()->where('id', $attributeID)->first()->sortedMedia->first()->preview_url,
                        'price' => $product->attributes()->where('id', $attributeID)->first()->price,
                        'quantity' => 1,
                    ]);

                    $this->emit('cartAddedOrUpdated');

                }
            }
        }else{
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
    }
    /**
     * @param int $productID
     * @param int|null $attributeID
     * @return void
     */
    public function addQuantity(int $productID, int|null $attributeID = null): void
    {
        if ($product = Product::when($attributeID,fn($q) => $q->whereHas('attributes', fn($q) => $q->where('id',$attributeID)))->where('id',$productID)->active()->first()) {
            if($attributeID) {
                if ($product->attributes()->where('id', $attributeID)->first()->quantity > 1) {
                    $this->cartService->addQuantity($product->id, $attributeID);
                }
            }else{
                if($product->quantity > 1) $this->cartService->addQuantity($product->id);
            }
            $this->emit('cartAddedOrUpdated');
        }
    }
    /**
     * @param int $productID
     * @param int|null $attributeID
     * @return void
     */

    public function removeQuantity(int $productID, int|null $attributeID = null): void
    {
        if ($product = Product::when($attributeID,fn($q) => $q->whereHas('attributes', fn($q) => $q->where('id',$attributeID)))->where('id',$productID)->active()->first()) {
            if($attributeID) {
                if ($product->attributes()->where('id', $attributeID)->first()->quantity > 1)  $this->cartService->removeQuantity($product->id, $attributeID);
            }else{
                if($product->quantity > 1) $this->cartService->removeQuantity($product->id);
            }
            $this->emit('cartAddedOrUpdated');
        }
    }
    /**
     * @return void
     */
    public function cartAddedOrUpdated(): void
    {
        $this->cartService->get();
    }
    public function updatedAttr()
    {
        $this->dispatchBrowserEvent('triggero');
    }
    public function render()
    {
        if($this->product->attributes()->count()){
            if(!$this->attr) {
                $this->productAttribute =   $this->product->attributes()->where('default',1)->first();
                $this->attr = $this->productAttribute ? $this->productAttribute->attributes()->get()->groupBy('attribute_group_id')->keys()->combine($this->productAttribute->attributes()->pluck('id'))->toArray() : [];
            }else{
                $this->productAttribute =   $this->product->attributes()->whereHas('attributes',function($q) {
                    $q->whereIn('id',$this->attr);})
                    ->first();
            }
            $attributesIDs = $this->product->attributes()->get()->map(fn($value) =>$value->attributes()->pluck('id'))->flatten(1)->unique()->toArray();
            $this->attributes = Attribute::query()->select('attributes.attribute_group_id','attributes.id','attribute_groups.is_color_group','attribute_groups.group_type','attribute_group_lang.name as group_name','attribute_lang.name as attribute_name')
                ->whereIn('attributes.id', $attributesIDs)
                ->leftJoin('attribute_lang','attributes.id','=','attribute_lang.attribute_id')
                ->leftJoin('attribute_groups','attributes.attribute_group_id','=','attribute_groups.id')
                ->leftJoin('attribute_group_lang','attributes.attribute_group_id','=','attribute_group_lang.attribute_group_id')
                ->where('attribute_group_lang.locale',app()->getLocale())
                ->where('attribute_lang.locale',app()->getLocale())
                ->get()
                ->groupBy('group_name')->toArray();
        }

        return view('livewire.product',['product','attributes','productAttribute']);
    }
}

<?php

namespace App\Http\Livewire;

use App\Models\AttributeProduct;
use App\Models\Product;
use Illuminate\Support\Collection;
use Livewire\Component;

class ProductMiniCard extends Component
{
    public \App\Models\Product $product;

    public ?int $activeAttribute = null;

    public $listeners = ['cartAddedOrUpdated', 'increase' => 'addQuantity', 'decrease' => 'removeQuantity'];

    protected \App\Services\Cart\Cart $cartService;

    public function boot(\App\Services\Cart\Cart $cart): void
    {
        $this->cartService = $cart;

    }

    public function mount(Product $product): void
    {
        $this->product = $product;
        $this->activeAttribute = $this->product->attributes()->where('default', 1)->first()?->id;

    }

    public function addToCart(int $productID, ?int $attributeID = null): void
    {
        if ($attributeID) {
            $attributeName = AttributeProduct::where('id', $attributeID)->first()->attributes()->first()->translate->name;
            if ($product = Product::where('id', $productID)->whereHas('attributes', fn ($q) => $q->where('id', $attributeID))->active()->first()) {
                if ($product->attributes()->where('id', $attributeID)->first()->quantity > 0) {
                    $this->cartService->add([
                        'product_id' => $product->id,
                        'attribute_id' => $attributeID,
                        'name' => $product->translate->name.' '.$attributeName,
                        'image' => $product->attributes()->where('id', $attributeID)->first()->sortedMedia->first()->preview_url,
                        'price' => $product->attributes()->where('id', $attributeID)->first()->price,
                        'quantity' => 1,
                    ]);

                    $this->emit('cartAddedOrUpdated');

                }
            }
        } else {
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

    public function addQuantity(int $productID, ?int $attributeID = null): void
    {
        if ($product = Product::when($attributeID, fn ($q) => $q->whereHas('attributes', fn ($q) => $q->where('id', $attributeID)))->where('id', $productID)->active()->first()) {
            if ($attributeID) {
                if ($product->attributes()->where('id', $attributeID)->first()->quantity > 1) {
                    $this->cartService->addQuantity($product->id, $attributeID);
                }
            } else {
                if ($product->quantity > 1) {
                    $this->cartService->addQuantity($product->id);
                }
            }
            $this->emit('cartAddedOrUpdated');
        }
    }

    public function removeQuantity(int $productID, ?int $attributeID = null): void
    {
        if ($product = Product::when($attributeID, fn ($q) => $q->whereHas('attributes', fn ($q) => $q->where('id', $attributeID)))->where('id', $productID)->active()->first()) {
            if ($attributeID) {
                if ($product->attributes()->where('id', $attributeID)->first()->quantity > 1) {
                    $this->cartService->removeQuantity($product->id, $attributeID);
                }
            } else {
                if ($product->quantity > 1) {
                    $this->cartService->removeQuantity($product->id);
                }
            }
            $this->emit('cartAddedOrUpdated');
        }
    }

    public function prepareProduct($product): Collection
    {
        $col = collect();
        $col->put('id', $product->id);
        $col->put('price', $product->price);
        $col->put('rebate', $product->rebate);
        $col->put('cost', $product->cost);
        $col->put('quantity', $product->quantity);
        $col->put('image', $product->mainImage);
        $col->put('reference', $product->reference);
        $col->put('name', $product->translate->name);
        $col->put('link_rewrite', $product->translate->link_rewrite);
        $col->put('attribute', collect([]));
        $col->put('allAttributes', []);
        if ($product->attributes->count()) {
            $attr = $product->attributes->where('id', $this->activeAttribute)->first();
            $allAttr = $product->attributes()->get();
            $col['attribute']->put('id', $attr->id);
            $col['attribute']->put('price', $attr->price);
            $col['attribute']->put('rebate', $attr->rebate);
            $col['attribute']->put('cost', $attr->cost);
            $col['attribute']->put('quantity', $attr->quantity);
            $col['attribute']->put('reference', $attr->reference);
            $col['attribute']->put('name', $attr->attributes()->get()->map(fn ($item) => $item->translate->name)->implode(','));
            $col['attribute']->put('image', $attr->sortedMedia->first());
            $col['attribute']->put('q', '?attr['.$attr->attributes()->get()->map(fn ($item) => $item->attribute_group_id)->implode(',').']='.$attr->attributes()->get()->map(fn ($item) => $item->id)->implode(','));
            $col['attribute']->put('attr', $attr->attributes()->get()->map(fn ($item) => ['id' => $item->id, 'name' => $item->translate->name, 'group_id' => $item->attribute_group_id, 'group_name' => $item->group->translate->name])->first());
            $col->put('allAttributes', $allAttr->map(fn ($value) => $value->attributes()->get()->map(fn ($item) => ['product_attribute_id' => $value->id, 'id' => $item->id, 'name' => $item->translate->name, 'group_id' => $item->attribute_group_id, 'group_name' => $item->group->translate->name]))->flatten(1)->groupBy('group_name'))->first();
        }

        return $col;
    }

    public function cartAddedOrUpdated(): void
    {
        $this->cartService->get();
    }

    public function render()
    {
        return view('livewire.product-mini-card', ['productCard' => $this->prepareProduct($this->product), 'activeAttribute']);
    }
}

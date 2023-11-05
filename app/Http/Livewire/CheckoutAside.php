<?php

namespace App\Http\Livewire;

use App\Models\Product;
use Livewire\Component;

class CheckoutAside extends Component
{
    public $cartProducts = null;

    public $summary;

    public $listeners = ['cartAddedOrUpdated', 'increase' => 'addQuantity', 'decrease' => 'removeQuantity'];

    protected $cartService;

    public function boot(\App\Services\Cart\Cart $cart)
    {
        $this->cartService = $cart;
    }

    public function cartAddedOrUpdated()
    {
        $this->cartProducts = $this->cartService->getItems();
        $this->summary = $this->cartService->getTotal();
        $this->cartService->get();
    }

    public function mount()
    {
        $this->cartProducts = $this->cartService->getItems();
        $this->summary = $this->cartService->getTotal();
    }

    public function addQuantity(int $productID, int $attributeID = null): void
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

    public function removeQuantity(int $productID, int $attributeID = null): void
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

    public function render()
    {

        return view('livewire.checkout-aside', ['cartProducts' => $this->cartProducts, 'summary' => $this->summary]);
    }
}

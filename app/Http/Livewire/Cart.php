<?php

namespace App\Http\Livewire;

use Livewire\Component;

class Cart extends Component
{
    public $cart = null;

    public $total = 0;

    public $totalQuantity = 0;

    protected $listeners = ['cartAddedOrUpdated'];

    protected $cartService;

    public function boot(\App\Services\Cart\Cart $cart)
    {
        $this->cartService = $cart;
    }

    public function cartAddedOrUpdated()
    {

        $this->cart = $this->cartService->getItems();
    }

    public function render()
    {
        $this->cart = $this->cartService->getItems();
        $this->total = $this->cartService->getTotal();
        $this->totalQuantity = $this->cartService->getTotalQuantity();
        $this->dispatchBrowserEvent('triggero');

        return view('livewire.cart', ['cartPrice' => $this->total, 'cartQuantity' => $this->totalQuantity, 'cart' => $this->cart]);
    }
}

<?php

namespace App\Http\Livewire;

use LivewireUI\Modal\ModalComponent;

class CartModal extends ModalComponent
{
    protected $cartProducts = null;

    public $summary;

    public $listeners = ['cartAddedOrUpdated'];

    protected $cartService;

    public function boot(\App\Services\Cart\Cart $cart)
    {
        $this->cartService = $cart;
    }

    public function cartAddedOrUpdated()
    {

        $this->cartProducts = $this->cartService->getItems();
        $this->summary = $this->cartService->getTotal();
    }

    public function mount()
    {
        $this->cartProducts = $this->cartService->getItems();
        $this->summary = $this->cartService->getTotal();
    }

    public function render()
    {
        return view('livewire.cart-modal', ['cartProducts' => $this->cartProducts, 'summary' => $this->summary]);
    }
}

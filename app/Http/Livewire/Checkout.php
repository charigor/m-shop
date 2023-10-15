<?php

namespace App\Http\Livewire;

use Spatie\LivewireWizard\Components\WizardComponent;

class Checkout extends  WizardComponent
{

    public function steps() : array
    {
        return [
            CheckoutStepComponent::class,
            DeliveryAddressStepComponent::class,
            PaymentOrderStepComponent::class,
        ];
    }
//    public function mount()
//    {
//        $this->fullname = auth()->user()->name;
//        $this->email = auth()->user()->email;
//    }
//    public function render()
//    {
//        return view('livewire.checkout');
//    }
}

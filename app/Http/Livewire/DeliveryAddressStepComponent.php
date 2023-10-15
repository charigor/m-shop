<?php

namespace App\Http\Livewire;

use Spatie\LivewireWizard\Components\StepComponent;

class DeliveryAddressStepComponent extends StepComponent
{
    public function render()
    {
        return view('livewire.checkout-delivery-step');
    }

}

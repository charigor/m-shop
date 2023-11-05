<?php

namespace App\Http\Livewire;

use Spatie\LivewireWizard\Components\StepComponent;

class PaymentOrderStepComponent extends StepComponent
{
    public function render()
    {
        return view('livewire.checkout-payment-step');
    }
}

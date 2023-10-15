<?php

namespace App\Http\Livewire;

use App\Models\Product;
use Livewire\Component;
use Spatie\LivewireWizard\Components\StepComponent;
use Spatie\LivewireWizard\Components\WizardComponent;

class PaymentOrderStepComponent extends StepComponent
{
    public function render()
    {
        return view('livewire.checkout-payment-step');
    }
}

<?php

namespace App\Services\Wizard;

use Spatie\LivewireWizard\Support\State;

class OrderWizardState extends State
{
    public function checkoutDeliveryStep(): array
    {
        $deliveryAddressStepState = $this->forStep('checkout-delivery-step');

        return [
            'name' => $deliveryAddressStepState['name'],
            'street' => $deliveryAddressStepState['street'],
            'zip' => $deliveryAddressStepState['zip'],
            'city' => $deliveryAddressStepState['city'],
        ];
    }

    public function amount(): int
    {
        return $this->forStep('cart')['amount'];
    }
}

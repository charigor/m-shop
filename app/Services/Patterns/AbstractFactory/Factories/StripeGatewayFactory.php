<?php

namespace App\Services\Patterns\AbstractFactory\Factories;

use App\Services\Patterns\AbstractFactory\Contracts\PaymentGatewayFactory;
use App\Services\Patterns\AbstractFactory\StripePaymentClient;
use App\Services\Patterns\AbstractFactory\StripeRefundClient;

class StripeGatewayFactory implements PaymentGatewayFactory {
    public function createPaymentClient(): StripePaymentClient
    {
        return new StripePaymentClient();
    }

    public function createRefundClient(): StripeRefundClient
    {
        return new StripeRefundClient();
    }
}

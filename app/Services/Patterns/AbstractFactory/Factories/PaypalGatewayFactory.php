<?php

namespace App\Services\Patterns\AbstractFactory\Factories;

use App\Services\Patterns\AbstractFactory\Contracts\PaymentGatewayFactory;
use App\Services\Patterns\AbstractFactory\PaypalPaymentClient;
use App\Services\Patterns\AbstractFactory\PaypalRefundClient;

class PaypalGatewayFactory implements PaymentGatewayFactory {
    public function createPaymentClient(): PaypalPaymentClient
    {
        return new PaypalPaymentClient();
    }

    public function createRefundClient(): PaypalRefundClient
    {
        return new PaypalRefundClient();
    }
}

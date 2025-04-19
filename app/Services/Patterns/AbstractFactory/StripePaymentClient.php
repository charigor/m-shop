<?php

namespace App\Services\Patterns\AbstractFactory;

use App\Services\Patterns\AbstractFactory\Contracts\PaymentClient;

class StripePaymentClient implements PaymentClient
{
    public function pay(float $amount): string {
        return "stripe_txn_" . uniqid();
    }
}

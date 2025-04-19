<?php

namespace App\Services\Patterns\AbstractFactory;

use App\Services\Patterns\AbstractFactory\Contracts\PaymentClient;

class PaypalPaymentClient implements PaymentClient {
    public function pay(float $amount): string {
        return "paypal_txn_" . $amount;
    }
}

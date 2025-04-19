<?php

namespace App\Services\Patterns\AbstractFactory;

use App\Services\Patterns\AbstractFactory\Contracts\RefundClient;

class StripeRefundClient implements RefundClient
{
    public function refund(string $transactionId): bool {
        return true;
    }
}

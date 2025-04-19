<?php

namespace App\Services\Patterns\AbstractFactory\Contracts;

interface RefundClient
{
    public function refund(string $transactionId): bool;
}

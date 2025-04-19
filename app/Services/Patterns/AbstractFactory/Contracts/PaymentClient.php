<?php

namespace App\Services\Patterns\AbstractFactory\Contracts;

interface PaymentClient
{
    public function pay(float $amount): string;
}

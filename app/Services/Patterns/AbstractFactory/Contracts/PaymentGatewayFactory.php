<?php

namespace App\Services\Patterns\AbstractFactory\Contracts;

interface PaymentGatewayFactory
{
    public function createPaymentClient(): PaymentClient;
    public function createRefundClient(): RefundClient;
}

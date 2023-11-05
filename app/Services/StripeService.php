<?php

namespace App\Services;

class StripeService implements Payment
{
    public function pay()
    {
        return 'stripe';
    }
}

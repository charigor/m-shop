<?php

namespace App\Services\Patterns\Adapter;

use App\Services\Patterns\Adapter\Contracts\SecondContract;

class Second implements SecondContract
{
    public function specificRequest(): string
    {
        return 'HI';
    }
}

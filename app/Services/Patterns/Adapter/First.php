<?php

namespace App\Services\Patterns\Adapter;

use App\Services\Patterns\Adapter\Contracts\FirstContract;

class First implements FirstContract
{
    public function request(): string
    {
        return 'What is your name?';
    }
}

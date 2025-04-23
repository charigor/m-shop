<?php

namespace App\Services\Patterns\Decorator\Contracts;

interface Formatter
{
    public function format(string $text): string;
}

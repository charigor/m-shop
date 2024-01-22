<?php

namespace App\Http\Controllers\Test\FactoryMethod;

interface FactoryMethodInterface
{
    public static function getConst(): mixed;

    public static function picture(): string;
}

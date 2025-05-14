<?php

namespace App\Http\Controllers\Test\FactoryMethod;

class FactoryMethod extends AbstractFactoryMethod
{
    public static function getConst(): mixed
    {
        return (new self)->getRef().' '.self::picture();
    }

    public static function picture(): string
    {
        // Read ne string
        return 'regular picture';
    }
}

<?php

namespace App\Http\Controllers\Test\FactoryMethod;

interface FactoryMethodInterface
{
    /**
     * @return mixed
     */
    public static function getConst(): mixed;
    public static function picture(): string;

}

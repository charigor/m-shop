<?php

namespace App\Http\Controllers\Test\FactoryMethod;

abstract class  AbstractFactoryMethod implements FactoryMethodInterface
{
    const FOOO = 124;
    private static string $ref = 'abstracting';
    abstract static function getConst(): mixed;

    private function some() :string
    {
        return "to do ";
    }

    /**
     * @return string
     */
    public function getRef() : string
    {
        return self::$ref . $this->some();
    }
    public static function picture(): string
    {
        return 'abstract picture';
    }

}

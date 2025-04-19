<?php

namespace App\Services\Patterns\FabricMethod\Test\Factories;
use \App\Services\Patterns\FabricMethod\Test\Contracts\FactoryContract;

abstract class Factory implements FactoryContract
{
     public abstract function make();
}

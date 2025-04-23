<?php

namespace App\Services\Patterns\Adapter\Contracts;

class Adapter
{
    public function __construct(private $object)
    {

    }

    public function specificRequest(){
        return $this->object->request();
    }
}

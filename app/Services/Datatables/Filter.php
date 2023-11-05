<?php

namespace App\Services\Datatables;

abstract class Filter
{
    protected $value;

    /**
     * Filter constructor.
     */
    public function __construct($requestColumnBlock)
    {
        $this->value = $requestColumnBlock;

        //        if($this->isJson($this->value)){
        //            $this->value = json_decode($this->value, true);
        //        }
    }

    abstract public function filter($query);

    private function isJson(string $json)
    {
        json_decode($json);
        if (json_last_error() === JSON_ERROR_NONE) {
            return true;
        }

        return false;
    }
}

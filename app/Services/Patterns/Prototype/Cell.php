<?php

namespace App\Services\Patterns\Prototype;

class Cell
{
    public $model;
    public function __construct(public $fields){
        $this->fields = $fields;
        $this->model = new Engine();
    }
    public function result() {
        return [
         'fields' => $this->fields,
         'model'  => $this->model
        ];
    }
}

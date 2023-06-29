<?php


namespace App\Services\Datatables\Categories\Filters;


use App\Services\Datatables\Filter;

class Name extends Filter
{

    public function filter($query)
    {
         $query->where('categories.name','LIKE','%'.$this->value.'%');
    }
}

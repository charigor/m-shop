<?php


namespace App\Services\Datatables\Langs\Filters;


use App\Services\Datatables\Filter;

class Name extends Filter
{

    public function filter($query)
    {
         $query->where('langs.name','LIKE','%'.$this->value.'%');
    }
}

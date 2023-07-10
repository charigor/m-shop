<?php


namespace App\Services\Datatables\Langs\Filters;


use App\Services\Datatables\Filter;

class Code extends Filter
{

    public function filter($query)
    {
         $query->where('langs.code','LIKE','%'.$this->value.'%');
    }
}

<?php


namespace App\Services\Datatables\Features\Filters;


use App\Services\Datatables\Filter;

class Name extends Filter
{


    public function filter($query)
    {
        $query->where('feature_lang.name','LIKE','%'.$this->value.'%');
    }
}

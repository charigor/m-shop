<?php


namespace App\Services\Datatables\Products\Filters;


use App\Services\Datatables\Filter;

class Reference extends Filter
{

    public function filter($query)
    {
         $query->where('products.reference','LIKE','%'.$this->value.'%');
    }
}

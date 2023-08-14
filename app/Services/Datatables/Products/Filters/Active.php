<?php


namespace App\Services\Datatables\Products\Filters;


use App\Services\Datatables\Filter;

class Active extends Filter
{

    public function filter($query)
    {
         $query->where('products.active',$this->value);
    }
}

<?php


namespace App\Services\Datatables\Categories\Filters;


use App\Services\Datatables\Filter;

class Active extends Filter
{

    public function filter($query)
    {
         $query->where('categories.active',$this->value);
    }
}

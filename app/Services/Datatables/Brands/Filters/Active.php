<?php


namespace App\Services\Datatables\Brands\Filters;


use App\Services\Datatables\Filter;

class Active extends Filter
{

    public function filter($query)
    {
        $query->where('brands.active',$this->value);
    }
}

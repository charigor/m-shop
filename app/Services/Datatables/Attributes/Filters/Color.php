<?php


namespace App\Services\Datatables\Attributes\Filters;


use App\Services\Datatables\Filter;

class Color extends Filter
{

    public function filter($query)
    {
        $query->where('attributes.color',$this->value);
    }
}

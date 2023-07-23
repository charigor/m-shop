<?php


namespace App\Services\Datatables\AttributeGroups\Filters;


use App\Services\Datatables\Filter;

class Amount extends Filter
{

    public function filter($query)
    {
        $query->having('amount','=',$this->value);
    }
}

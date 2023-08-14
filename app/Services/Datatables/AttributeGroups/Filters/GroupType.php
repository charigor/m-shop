<?php


namespace App\Services\Datatables\AttributeGroups\Filters;


use App\Services\Datatables\Filter;

class GroupType extends Filter
{

    public function filter($query)
    {
         $query->where('attribute_groups.group_type',$this->value);
    }
}

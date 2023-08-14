<?php


namespace App\Services\Datatables\AttributeGroups\Filters;


use App\Services\Datatables\Filter;

class Name extends Filter
{


    public function filter($query)
    {
        $query->where('attribute_group_lang.name','LIKE','%'.$this->value.'%');
    }
}

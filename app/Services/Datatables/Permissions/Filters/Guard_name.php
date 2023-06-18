<?php


namespace App\Services\Datatables\Permissions\Filters;


use App\Services\Datatables\Filter;

class Guard_name extends Filter
{

    public function filter($query)
    {
         $query->where('permissions.guard_name','LIKE','%'.$this->value.'%');
    }
}

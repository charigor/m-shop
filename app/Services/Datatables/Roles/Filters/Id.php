<?php


namespace App\Services\Datatables\Roles\Filters;


use App\Services\Datatables\Filter;

class Id extends Filter
{

    public function filter($query)
    {
        $query->where('roles.id', $this->value);
    }
}

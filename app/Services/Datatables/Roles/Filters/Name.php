<?php

namespace App\Services\Datatables\Roles\Filters;

use App\Services\Datatables\Filter;

class Name extends Filter
{
    public function filter($query)
    {
        $query->where('roles.name', 'LIKE', '%'.$this->value.'%');
    }
}

<?php

namespace App\Services\Datatables\Roles\Filters;

use App\Services\Datatables\Filter;

class Guard_name extends Filter
{
    public function filter($query)
    {
        $query->where('roles.guard_name', 'LIKE', '%'.$this->value.'%');
    }
}

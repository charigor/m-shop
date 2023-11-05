<?php

namespace App\Services\Datatables\Permissions\Filters;

use App\Services\Datatables\Filter;

class Name extends Filter
{
    public function filter($query)
    {
        $query->where('permissions.name', 'LIKE', '%'.$this->value.'%');
    }
}

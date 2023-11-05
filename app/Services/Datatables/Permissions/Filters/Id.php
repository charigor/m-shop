<?php

namespace App\Services\Datatables\Permissions\Filters;

use App\Services\Datatables\Filter;

class Id extends Filter
{
    public function filter($query)
    {
        $query->where('permissions.id', $this->value);
    }
}

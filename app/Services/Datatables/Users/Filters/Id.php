<?php

namespace App\Services\Datatables\Users\Filters;

use App\Services\Datatables\Filter;

class Id extends Filter
{
    public function filter($query)
    {
        $query->where('users.id', $this->value);
    }
}

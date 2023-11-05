<?php

namespace App\Services\Datatables\Users\Filters;

use App\Services\Datatables\Filter;

class Roles extends Filter
{
    public function filter($query)
    {
        $query->whereHas('roles', function ($q) {
            $q->where('id', '=', $this->value);
        });
    }
}

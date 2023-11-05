<?php

namespace App\Services\Datatables\Users\Filters;

use App\Services\Datatables\Filter;

class Name extends Filter
{
    public function filter($query)
    {
        $query->where('users.name', 'LIKE', '%'.$this->value.'%');
    }
}

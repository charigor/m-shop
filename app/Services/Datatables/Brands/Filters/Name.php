<?php

namespace App\Services\Datatables\Brands\Filters;

use App\Services\Datatables\Filter;

class Name extends Filter
{
    public function filter($query)
    {
        $query->where('brands.name', 'LIKE', '%'.$this->value.'%');
    }
}

<?php

namespace App\Services\Datatables\Brands\Filters;

use App\Services\Datatables\Filter;

class Created_at extends Filter
{
    public function filter($query)
    {
        $query->whereBetween('brands.created_at', $this->value);
    }
}

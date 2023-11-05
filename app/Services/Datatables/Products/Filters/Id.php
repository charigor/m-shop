<?php

namespace App\Services\Datatables\Products\Filters;

use App\Services\Datatables\Filter;

class Id extends Filter
{
    public function filter($query)
    {
        $query->where('products.id', $this->value);
    }
}

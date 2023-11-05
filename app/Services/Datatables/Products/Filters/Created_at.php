<?php

namespace App\Services\Datatables\Products\Filters;

use App\Services\Datatables\Filter;

class Created_at extends Filter
{
    public function filter($query)
    {
        $query->whereBetween('products.created_at', $this->value);
    }
}

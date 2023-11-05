<?php

namespace App\Services\Datatables\Products\Filters;

use App\Services\Datatables\Filter;

class Updated_at extends Filter
{
    public function filter($query)
    {
        $query->whereBetween('products.updated_at', $this->value);
    }
}

<?php

namespace App\Services\Datatables\Attributes\Filters;

use App\Services\Datatables\Filter;

class Id extends Filter
{
    public function filter($query)
    {
        $query->where('attributes.id', $this->value);
    }
}

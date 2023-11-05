<?php

namespace App\Services\Datatables\Features\Filters;

use App\Services\Datatables\Filter;

class Position extends Filter
{
    public function filter($query)
    {
        $query->where('features.position', $this->value);
    }
}

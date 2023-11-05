<?php

namespace App\Services\Datatables\FeatureValues\Filters;

use App\Services\Datatables\Filter;

class Id extends Filter
{
    public function filter($query)
    {
        $query->where('feature_values.id', $this->value);
    }
}

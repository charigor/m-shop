<?php

namespace App\Services\Datatables\FeatureValues\Filters;

use App\Services\Datatables\Filter;

class Value extends Filter
{
    public function filter($query)
    {
        $query->where('feature_value_lang.value', 'LIKE', '%'.$this->value.'%');
    }
}

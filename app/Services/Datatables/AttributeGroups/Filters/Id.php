<?php

namespace App\Services\Datatables\AttributeGroups\Filters;

use App\Services\Datatables\Filter;

class Id extends Filter
{
    public function filter($query)
    {
        $query->where('attribute_groups.id', $this->value);
    }
}

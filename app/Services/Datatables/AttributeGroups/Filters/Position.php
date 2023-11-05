<?php

namespace App\Services\Datatables\AttributeGroups\Filters;

use App\Services\Datatables\Filter;

class Position extends Filter
{
    public function filter($query)
    {
        $query->where('attribute_groups.position', $this->value);
    }
}

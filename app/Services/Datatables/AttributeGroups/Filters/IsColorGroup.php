<?php

namespace App\Services\Datatables\AttributeGroups\Filters;

use App\Services\Datatables\Filter;

class IsColorGroup extends Filter
{
    public function filter($query)
    {
        $query->where('attribute_groups.is_color_group', $this->value);
    }
}

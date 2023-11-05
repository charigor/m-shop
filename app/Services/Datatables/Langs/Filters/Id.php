<?php

namespace App\Services\Datatables\Langs\Filters;

use App\Services\Datatables\Filter;

class Id extends Filter
{
    public function filter($query)
    {
        $query->where('langs.id', $this->value);
    }
}

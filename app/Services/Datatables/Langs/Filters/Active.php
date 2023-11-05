<?php

namespace App\Services\Datatables\Langs\Filters;

use App\Services\Datatables\Filter;

class Active extends Filter
{
    public function filter($query)
    {
        $query->where('langs.active', $this->value);
    }
}

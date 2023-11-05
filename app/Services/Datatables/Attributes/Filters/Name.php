<?php

namespace App\Services\Datatables\Attributes\Filters;

use App\Services\Datatables\Filter;

class Name extends Filter
{
    public function filter($query)
    {
        $query->where('attribute_lang.name', 'LIKE', '%'.$this->value.'%');
    }
}

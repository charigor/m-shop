<?php

namespace App\Services\Datatables\Products\Filters;

use App\Services\Datatables\Filter;

class Name extends Filter
{
    public function filter($query)
    {
        $query->where('product_lang.name', 'LIKE', '%'.$this->value.'%');
    }
}

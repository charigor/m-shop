<?php

namespace App\Services\Datatables\Categories\Filters;

use App\Services\Datatables\Filter;

class Title extends Filter
{
    public function filter($query)
    {
        $query->where('category_lang.title', 'LIKE', '%'.$this->value.'%');
    }
}

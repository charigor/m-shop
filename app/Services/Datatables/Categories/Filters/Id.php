<?php


namespace App\Services\Datatables\Categories\Filters;


use App\Services\Datatables\Filter;

class Id extends Filter
{

    public function filter($query)
    {
        $query->where('categories.id', $this->value);
    }
}

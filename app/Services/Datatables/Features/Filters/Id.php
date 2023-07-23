<?php


namespace App\Services\Datatables\Features\Filters;


use App\Services\Datatables\Filter;

class Id extends Filter
{

    public function filter($query)
    {
        $query->where('features.id', $this->value);
    }
}

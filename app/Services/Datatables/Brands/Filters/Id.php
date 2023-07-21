<?php


namespace App\Services\Datatables\Brands\Filters;


use App\Services\Datatables\Filter;

class Id extends Filter
{

    public function filter($query)
    {
        $query->where('brands.id', $this->value);
    }
}

<?php


namespace App\Services\Datatables\Categories\Filters;


use App\Services\Datatables\Filter;

class Created_at extends Filter
{


    public function filter($query)
    {
        $query->whereBetween('categories.created_at', $this->value);
    }
}

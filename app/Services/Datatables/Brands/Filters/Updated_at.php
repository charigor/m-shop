<?php


namespace App\Services\Datatables\Brands\Filters;


use App\Services\Datatables\Filter;

class Updated_at extends Filter
{


    public function filter($query)
    {
        $query->whereBetween('brands.updated_at', $this->value);
    }
}

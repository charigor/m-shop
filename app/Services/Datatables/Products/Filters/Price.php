<?php


namespace App\Services\Datatables\Products\Filters;


use App\Services\Datatables\Filter;

class Price extends Filter
{

    public function filter($query)
    {
        $query->where('products.price', $this->value);
    }
}

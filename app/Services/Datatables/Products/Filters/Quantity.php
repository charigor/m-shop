<?php


namespace App\Services\Datatables\Products\Filters;


use App\Services\Datatables\Filter;

class Quantity extends Filter
{

    public function filter($query)
    {
        $query->where('products.quantity', $this->value);
    }
}

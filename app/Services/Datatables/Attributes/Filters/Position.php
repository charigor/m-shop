<?php


namespace App\Services\Datatables\Attributes\Filters;


use App\Services\Datatables\Filter;

class Position extends Filter
{
    public function filter($query)
    {
        $query->where('attributes.position', $this->value);
    }
}

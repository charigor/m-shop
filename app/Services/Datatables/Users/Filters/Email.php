<?php


namespace App\Services\Datatables\Users\Filters;


use App\Services\Datatables\Filter;

class Email extends Filter
{

    public function filter($query)
    {
        $query->where('users.email','LIKE','%'.$this->value.'%');
    }
}

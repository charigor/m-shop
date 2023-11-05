<?php

namespace App\Services\Datatables\Langs\Filters;

use App\Models\Lang;
use App\Services\Datatables\Filter;

class DateFormat extends Filter
{
    public function filter($query)
    {
        $query->when($this->value, fn ($query) => $query->where('langs.date_format', Lang::DATE_FORMAT[$this->value]));
    }
}

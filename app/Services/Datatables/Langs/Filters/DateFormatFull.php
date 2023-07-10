<?php


namespace App\Services\Datatables\Langs\Filters;


use App\Models\Lang;
use App\Services\Datatables\Filter;

class DateFormatFull extends Filter
{

    public function filter($query)
    {
        $query->when($this->value,fn($query) => $query->where('langs.date_format_full', Lang::DATE_FORMAT_FULL[$this->value]));
    }
}

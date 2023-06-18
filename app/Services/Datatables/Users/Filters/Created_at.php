<?php


namespace App\Services\Datatables\Users\Filters;


use App\Services\Datatables\Filter;
use Carbon\Carbon;

class Created_at extends Filter
{


    public function filter($query)
    {
        $query->whereBetween('users.created_at', $this->value);

    }
    public function array_swap(array &$array, $key, $key2)
    {
        if (isset($array[$key]) && isset($array[$key2])) {
            list($array[$key], $array[$key2]) = array($array[$key2], $array[$key]);
        }

        return $array;
    }
    public function splitDaytime($date)
    {
        $dt = Carbon::createFromFormat('Y-m-d',$date);
        $startDay = $dt->copy()->startOfDay();
        $endDay = $dt->copy()->endOfDay();
        return [$startDay,$endDay];
    }

    public function compareDates($date1,$date2): bool
    {
        $date1 = Carbon::createFromFormat('Y-m-d', $date1);
        $date2 = Carbon::createFromFormat('Y-m-d', $date2);

        return $date1->gte($date2);
    }
}

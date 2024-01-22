<?php

if (! function_exists('createOptions')) {
    function createOptions($val, $default = null): Illuminate\Support\Collection
    {
        $collection = collect($val);
        $collection = $collection->map(fn ($value, $key) => ['key' => $key, 'value' => $value]);

        return $default ? $collection->prepend(['key' => null, 'value' => $default]) : $collection;
    }
}
if (! function_exists('modifyRequestForPivot')) {
    function modifyRequestForPivot($data, $key, $value): Illuminate\Support\Collection
    {
        return collect($data)->keyBy($key)->map(fn ($item) => [$value => $item[$value]]);
    }
}
if (! function_exists('priceFormat')) {
    function priceFormat($number, $n = 2)
    {
        return number_format((float) $number, $n, '.', '');
    }
}
if (! function_exists('getStringBetween')) {
    function getStringBetween($string, $start, $end): string
    {
        $string = ' '.$string;
        $ini = strpos($string, $start);
        if ($ini == 0) {
            return '';
        }
        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;

        return substr($string, $ini, $len);
    }
}

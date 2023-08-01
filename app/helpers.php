<?php

if(!function_exists('createOptions')){
    function createOptions($val,$default = null): \Illuminate\Support\Collection
    {
        $collection = collect($val);
        $collection = $collection->map(fn($value,$key) =>  ['key' => $key,'value' =>  $value]);
        return $default ? $collection->prepend(['key' => null,'value' => $default]) : $collection;
    }
}
if(!function_exists('modifyRequestForPivot')){
    function modifyRequestForPivot($data,$key,$value): \Illuminate\Support\Collection
    {
        return collect($data)->keyBy($key)->map(fn($item) => [$value => $item[$value]]);
    }
}

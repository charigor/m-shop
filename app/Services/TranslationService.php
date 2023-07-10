<?php


namespace App\Services;

class TranslationService
{
    /**
     * @param array $data
     * @param array $require_fields
     * @return array
     */
    public function prepareFields( array $data,array $require_fields = []): array
    {
        $result = [];
        $count = 0;
        foreach($data as $key => $value){
             $result[$count] =  ['locale' => $key];
            foreach($value as $nestedKey => $nestedValue){
                $result[$count][$nestedKey] = $nestedValue ?? (in_array($nestedKey,$require_fields) ? $data[app()->getLocale()][$nestedKey] : "");
            }
            $count++;
        }

        return $result;
    }
}

<?php

namespace App\Services\Filter\Elastic;

/**
 * Brand filter strategy
 */
class BrandFilterStrategy implements FilterStrategyInterface
{
    public function processInput($input): array
    {
        return is_string($input) ? explode(',', $input) : $input;
    }

    public function buildFilter($brands): array
    {
        if (empty($brands)) {
            return [];
        }

        return [
            'terms' => [
                'brand_id' => $brands,
            ],
        ];
    }
}

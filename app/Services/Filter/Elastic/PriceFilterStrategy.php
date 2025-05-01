<?php

namespace App\Services\Filter\Elastic;

/**
 * Price filter strategy
 */
class PriceFilterStrategy implements FilterStrategyInterface
{
    public function processInput($input): array
    {
        return is_string($input) ? explode(',', $input) : $input;
    }

    public function buildFilter($price): array
    {
        if (empty($price)) {
            return [];
        }

        $range = [];
        foreach ($price as $part) {
            if (str_starts_with($part, 'min')) {
                $range['gte'] = (int) str_replace('min', '', $part);
            }
            if (str_starts_with($part, 'max')) {
                $range['lte'] = (int) str_replace('max', '', $part);
            }
        }

        if (empty($range)) {
            return [];
        }

        return [
            'range' => [
                'price' => $range,
            ],
        ];
    }
}

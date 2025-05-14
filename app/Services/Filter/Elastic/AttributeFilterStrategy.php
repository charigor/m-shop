<?php

namespace App\Services\Filter\Elastic;

/**
 * Attribute filter strategy
 */
class AttributeFilterStrategy implements FilterStrategyInterface
{
    private string $locale;

    public function __construct(string $locale)
    {
        $this->locale = $locale;
    }

    public function processInput($input): array
    {
        $attributes = $input;

        foreach ($attributes as $key => $value) {
            if (is_string($value)) {
                $attributes[$key] = explode(',', $value);
            }
        }

        return $attributes;
    }

    public function buildFilter($attributes): array
    {
        if (empty($attributes)) {
            return [];
        }

        $filters = [];

        foreach ($attributes as $attributeName => $values) {
            $filters[] = [
                'nested' => [
                    'path' => 'product_attributes',
                    'query' => [
                        'bool' => [
                            'must' => [
                                [
                                    'term' => [
                                        'product_attributes.attribute_name.'.$this->locale => $attributeName,
                                    ],
                                ],
                                [
                                    'terms' => [
                                        'product_attributes.attribute_value.'.$this->locale => $values,
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ];
        }

        return $filters;
    }
}

<?php

namespace App\Services\Filter\Elastic;

/**
 * Feature filter strategy
 */
class FeatureFilterStrategy implements FilterStrategyInterface
{
    private string $locale;

    public function __construct(string $locale)
    {
        $this->locale = $locale;
    }

    public function processInput($input): array
    {
        $features = $input;

        foreach ($features as $key => $value) {
            if (is_string($value)) {
                $features[$key] = explode(',', $value);
            }
        }

        return $features;
    }

    public function buildFilter($features): array
    {
        if (empty($features)) {
            return [];
        }

        $filters = [];

        foreach ($features as $featureName => $values) {
            $filters[] = [
                'nested' => [
                    'path' => 'product_features',
                    'query' => [
                        'bool' => [
                            'must' => [
                                [
                                    'term' => [
                                        'product_features.feature_name.'.$this->locale => $featureName,
                                    ],
                                ],
                                [
                                    'terms' => [
                                        'product_features.feature_value.'.$this->locale => $values,
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

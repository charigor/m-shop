<?php

namespace App\Services\Filter\Elastic;

/**
 * Builder class for Elasticsearch aggregations
 */
class AggregationBuilder
{
    private string $locale;

    public function __construct(string $locale)
    {
        $this->locale = $locale;
    }

    /**
     * Build aggregations for metadata (brands, attributes, features)
     */
    public function buildMetadataAggregations(): array
    {
        return [
            'all_brands' => [
                'terms' => [
                    'field' => 'brand_id',
                    'size' => 100,
                ],
                'aggs' => [
                    'brand_details' => [
                        'top_hits' => [
                            'size' => 1,
                            '_source' => ['brand_name'],
                        ],
                    ],
                ],
            ],
            'all_attributes' => [
                'nested' => [
                    'path' => 'product_attributes',
                ],
                'aggs' => [
                    'attributes' => [
                        'terms' => [
                            'field' => 'product_attributes.attribute_name.' . $this->locale,
                            'size' => 100,
                        ],
                        'aggs' => [
                            'attribute_values' => [
                                'terms' => [
                                    'field' => 'product_attributes.attribute_value.' . $this->locale,
                                    'size' => 100,
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'all_features' => [
                'nested' => [
                    'path' => 'product_features',
                ],
                'aggs' => [
                    'features' => [
                        'terms' => [
                            'field' => 'product_features.feature_name.' . $this->locale,
                            'size' => 100,
                        ],
                        'aggs' => [
                            'feature_values' => [
                                'terms' => [
                                    'field' => 'product_features.feature_value.' . $this->locale,
                                    'size' => 100,
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * Build aggregation for attribute values
     */
    public function buildAttributeValueAggregation(string $attributeName): array
    {
        return [
            'filtered_attributes' => [
                'nested' => [
                    'path' => 'product_attributes',
                ],
                'aggs' => [
                    'attribute_name_filter' => [
                        'filter' => [
                            'term' => [
                                'product_attributes.attribute_name.' . $this->locale => $attributeName,
                            ],
                        ],
                        'aggs' => [
                            'attribute_values' => [
                                'terms' => [
                                    'field' => 'product_attributes.attribute_value.' . $this->locale,
                                    'size' => 100,
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * Build aggregation for feature values
     */
    public function buildFeatureValueAggregation(string $featureName): array
    {
        return [
            'filtered_features' => [
                'nested' => [
                    'path' => 'product_features',
                ],
                'aggs' => [
                    'feature_name_filter' => [
                        'filter' => [
                            'term' => [
                                'product_features.feature_name.' . $this->locale => $featureName,
                            ],
                        ],
                        'aggs' => [
                            'feature_values' => [
                                'terms' => [
                                    'field' => 'product_features.feature_value.' . $this->locale,
                                    'size' => 100,
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}

<?php

namespace App\Services\Filter;

use Elastic\Elasticsearch\Client;

class ElasticFilter
{
    private $hasProductFeatures = false;

    public function __construct(private Client $client)
    {
        // Check if product_features field exists and is nested when the class is instantiated
//        $this->hasProductFeatures = $this->checkIfNestedFieldExists('products_index', 'product_features');
    }

    public function handle($cat_id, $filters = [])
    {
        info($filters);
        $brands = isset($filters['brands']) && !empty($filters['brands']) ? explode(',', $filters['brands']) : [];
        $price = isset($filters['price']) && !empty($filters['price']) ? explode(',', $filters['price']) : [];
        $attributes = isset($filters['attr']) && !empty($filters['attr']) ? $filters['attr'] : [];
        $features = isset($filters['features']) && !empty($filters['features']) ? $filters['features'] : [];

        // Process string values in attributes
        foreach ($attributes as $key => $value) {
            if (is_string($value)) {
                $attributes[$key] = explode(',', $value);
            }
        }

        // Process string values in features (only if features exist)
        foreach ($features as $key => $value) {
            if (is_string($value)) {
                $features[$key] = explode(',', $value);
            }
        }

        // Create price filter
        $priceFilter = [];
        if (!empty($price)) {
            $range = [];
            foreach ($price as $part) {
                if (str_starts_with($part, 'min')) {
                    $range['gte'] = (int)str_replace('min', '', $part);
                }
                if (str_starts_with($part, 'max')) {
                    $range['lte'] = (int)str_replace('max', '', $part);
                }
            }

            if (!empty($range)) {
                $priceFilter = [
                    'range' => [
                        'price' => $range,
                    ],
                ];
            }
        }

        // Create brand filter
        $brandFilter = [];
        if (!empty($brands)) {
            $brandFilter = [
                'terms' => [
                    'brand_id' => $brands,
                ],
            ];
        }

        // Create attribute filters by group
        $attributeFilters = [];
        $attributeGroupFilters = []; // Store filters by attribute group

        if (!empty($attributes)) {
            foreach ($attributes as $attributeName => $values) {
                $filter = [
                    'nested' => [
                        'path' => 'product_attributes',
                        'query' => [
                            'bool' => [
                                'must' => [
                                    [
                                        'term' => [
                                            'product_attributes.attribute_name.' . app()->getLocale() => $attributeName,
                                        ],
                                    ],
                                    [
                                        'terms' => [
                                            'product_attributes.attribute_value.' . app()->getLocale() => $values,
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ];

                $attributeFilters[] = $filter;
                $attributeGroupFilters[$attributeName] = $filter;
            }
        }

        // Create feature filters by group (only if features exist)
        $featureFilters = [];
        $featureGroupFilters = []; // Store filters by feature group

        if (!empty($features)) {
            foreach ($features as $featureName => $values) {
                $filter = [
                    'nested' => [
                        'path' => 'product_features',
                        'query' => [
                            'bool' => [
                                'must' => [
                                    [
                                        'term' => [
                                            'product_features.feature_name.' . app()->getLocale() => $featureName,
                                        ],
                                    ],
                                    [
                                        'terms' => [
                                            'product_features.feature_value.' . app()->getLocale() => $values,
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ];

                $featureFilters[] = $filter;
                $featureGroupFilters[$featureName] = $filter;
            }
        }

        // Build aggregations
        $aggregations = [
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
                            'field' => 'product_attributes.attribute_name.' . app()->getLocale(),
                            'size' => 100,
                        ],
                        'aggs' => [
                            'attribute_values' => [
                                'terms' => [
                                    'field' => 'product_attributes.attribute_value.' . app()->getLocale(),
                                    'size' => 100,
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        // Only add features aggregation if the field exists
//        if ($this->hasProductFeatures) {
            $aggregations['all_features'] = [
                'nested' => [
                    'path' => 'product_features',
                ],
                'aggs' => [
                    'features' => [
                        'terms' => [
                            'field' => 'product_features.feature_name.' . app()->getLocale(),
                            'size' => 100,
                        ],
                        'aggs' => [
                            'feature_values' => [
                                'terms' => [
                                    'field' => 'product_features.feature_value.' . app()->getLocale(),
                                    'size' => 100,
                                ],
                            ],
                        ],
                    ],
                ],
            ];
//        }

        // 1. Get all brands, attributes, and features in category (no filters applied)
        $allMetadataBody = [
            'size' => 0,
            'query' => [
                'term' => [
                    'category_id' => $cat_id,
                ],
            ],
            'aggs' => $aggregations,
        ];

        $allMetadataResponse = $this->search('products_index', $allMetadataBody);
        $allBrandBuckets = $allMetadataResponse['aggregations']['all_brands']['buckets'] ?? [];
        $attributeBuckets = collect($allMetadataResponse['aggregations']['all_attributes']['attributes']['buckets'] ?? []);

        // Only get feature buckets if the field exists
        $featureBuckets = collect([]);
//        if ($this->hasProductFeatures) {
            $featureBuckets = collect($allMetadataResponse['aggregations']['all_features']['features']['buckets'] ?? []);
//        }

        // 2. Create filter combinations

        // Only include non-empty filters
        $priceOnlyFilters = !empty($priceFilter) ? [$priceFilter] : [];
        $brandOnlyFilters = !empty($brandFilter) ? [$brandFilter] : [];

        // Create filter set for brands (price + all attribute filters + all feature filters)
        $brandsWithOtherFilters = array_merge($priceOnlyFilters, $attributeFilters, $featureFilters);

        // 3. Get brands with all other filters applied
        $brandsWithFiltersBody = [
            'size' => 0,
            'query' => [
                'bool' => [
                    'must' => [
                        ['term' => ['category_id' => $cat_id]],
                    ],
                ],
            ],
            'aggs' => [
                'brands' => [
                    'terms' => [
                        'field' => 'brand_id',
                        'size' => 100,
                    ],
                ],
            ],
        ];

        // Only add filter if there are actual filters to apply
        if (!empty($brandsWithOtherFilters)) {
            $brandsWithFiltersBody['query']['bool']['filter'] = $brandsWithOtherFilters;
        }

        $brandsWithFiltersResponse = $this->search('products_index', $brandsWithFiltersBody);
        $brandsWithFiltersMap = collect($brandsWithFiltersResponse['aggregations']['brands']['buckets'] ?? [])
            ->mapWithKeys(function ($bucket) {
                return [$bucket['key'] => $bucket['doc_count']];
            });

        // 4. For each attribute group, get counts excluding its own filter
        $attributeCountMaps = [];

        foreach ($attributeBuckets as $attributeBucket) {
            $attributeName = $attributeBucket['key'];

            // Create filters excluding the current attribute group
            $filtersExcludingCurrentAttribute = array_merge(
                $priceOnlyFilters,
                $brandOnlyFilters,
                $featureFilters // Include all feature filters
            );

            // Add filters for all other attribute groups
            foreach ($attributeGroupFilters as $attrName => $filter) {
                if ($attrName !== $attributeName) {
                    $filtersExcludingCurrentAttribute[] = $filter;
                }
            }

            // Query Elasticsearch with these filters
            $attributeWithFiltersBody = [
                'size' => 0,
                'query' => [
                    'bool' => [
                        'must' => [
                            ['term' => ['category_id' => $cat_id]],
                        ],
                    ],
                ],
                'aggs' => [
                    'filtered_attributes' => [
                        'nested' => [
                            'path' => 'product_attributes',
                        ],
                        'aggs' => [
                            'attribute_name_filter' => [
                                'filter' => [
                                    'term' => [
                                        'product_attributes.attribute_name.' . app()->getLocale() => $attributeName,
                                    ],
                                ],
                                'aggs' => [
                                    'attribute_values' => [
                                        'terms' => [
                                            'field' => 'product_attributes.attribute_value.' . app()->getLocale(),
                                            'size' => 100,
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ];

            // Only add filter if there are actual filters to apply
            if (!empty($filtersExcludingCurrentAttribute)) {
                $attributeWithFiltersBody['query']['bool']['filter'] = $filtersExcludingCurrentAttribute;
            }

            $attributeWithFiltersResponse = $this->search('products_index', $attributeWithFiltersBody);
            $valueBuckets = $attributeWithFiltersResponse['aggregations']['filtered_attributes']['attribute_name_filter']['attribute_values']['buckets'] ?? [];

            // Create count map for this attribute
            $attributeCountMaps[$attributeName] = collect($valueBuckets)
                ->mapWithKeys(function ($bucket) {
                    return [$bucket['key'] => $bucket['doc_count']];
                })
                ->all();
        }

        // 5. For each feature group, get counts excluding its own filter (only if features exist)
        $featureCountMaps = [];

//        if ($this->hasProductFeatures) {
            foreach ($featureBuckets as $featureBucket) {
                $featureName = $featureBucket['key'];

                // Create filters excluding the current feature group
                $filtersExcludingCurrentFeature = array_merge(
                    $priceOnlyFilters,
                    $brandOnlyFilters,
                    $attributeFilters // Include all attribute filters
                );

                // Add filters for all other feature groups
                foreach ($featureGroupFilters as $featName => $filter) {
                    if ($featName !== $featureName) {
                        $filtersExcludingCurrentFeature[] = $filter;
                    }
                }

                // Query Elasticsearch with these filters
                $featureWithFiltersBody = [
                    'size' => 0,
                    'query' => [
                        'bool' => [
                            'must' => [
                                ['term' => ['category_id' => $cat_id]],
                            ],
                        ],
                    ],
                    'aggs' => [
                        'filtered_features' => [
                            'nested' => [
                                'path' => 'product_features',
                            ],
                            'aggs' => [
                                'feature_name_filter' => [
                                    'filter' => [
                                        'term' => [
                                            'product_features.feature_name.' . app()->getLocale() => $featureName,
                                        ],
                                    ],
                                    'aggs' => [
                                        'feature_values' => [
                                            'terms' => [
                                                'field' => 'product_features.feature_value.' . app()->getLocale(),
                                                'size' => 100,
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ];

                // Only add filter if there are actual filters to apply
                if (!empty($filtersExcludingCurrentFeature)) {
                    $featureWithFiltersBody['query']['bool']['filter'] = $filtersExcludingCurrentFeature;
                }

                $featureWithFiltersResponse = $this->search('products_index', $featureWithFiltersBody);
                $valueBuckets = $featureWithFiltersResponse['aggregations']['filtered_features']['feature_name_filter']['feature_values']['buckets'] ?? [];

                // Create count map for this feature
                $featureCountMaps[$featureName] = collect($valueBuckets)
                    ->mapWithKeys(function ($bucket) {
                        return [$bucket['key'] => $bucket['doc_count']];
                    })
                    ->all();
            }
//        }

        // 6. Get all filtered products (apply all filters)
        $allFilters = [];
        if (!empty($priceFilter)) {
            $allFilters[] = $priceFilter;
        }
        if (!empty($brandFilter)) {
            $allFilters[] = $brandFilter;
        }
        // Add all attribute filters
        foreach ($attributeFilters as $filter) {
            $allFilters[] = $filter;
        }
        // Add all feature filters
        foreach ($featureFilters as $filter) {
            $allFilters[] = $filter;
        }

        $filteredProductsBody = [
            'size' => 0,
            'query' => [
                'bool' => [
                    'must' => [
                        ['term' => ['category_id' => $cat_id]],
                    ],
                ],
            ],
            'aggs' => [
                'brands' => [
                    'terms' => [
                        'field' => 'brand_id',
                        'size' => 100,
                    ],
                    'aggs' => [
                        'products' => [
                            'top_hits' => [
                                'size' => 100,
                                '_source' => ['product_id', 'brand_id'],
                            ],
                        ],
                        'min_price' => [
                            'min' => [
                                'field' => 'price',
                            ],
                        ],
                        'max_price' => [
                            'max' => [
                                'field' => 'price',
                            ],
                        ],
                    ],
                ],
            ],
        ];

        // Only add filter if there are actual filters to apply
        if (!empty($allFilters)) {
            $filteredProductsBody['query']['bool']['filter'] = $allFilters;
        }

        $filteredProductsResponse = $this->search('products_index', $filteredProductsBody);
        $filteredBuckets = $filteredProductsResponse['aggregations']['brands']['buckets'] ?? [];

        // Extract product IDs from filtered results
        $productIds = collect($filteredBuckets)->flatMap(function ($bucket) {
            return collect($bucket['products']['hits']['hits'] ?? [])
                ->map(function ($hit) {
                    return $hit['_source']['product_id'];
                });
        })->unique()->values()->all();

        // Calculate price range from filtered products
        $minPrice = collect($filteredBuckets)->pluck('min_price.value')->filter()->min();
        $maxPrice = collect($filteredBuckets)->pluck('max_price.value')->filter()->max();

        // Format attributes with adaptive counts - using the per-attribute group filters
        $attributesData = $attributeBuckets->mapWithKeys(function ($attribute) use ($attributeCountMaps) {
            $attributeName = $attribute['key'];
            $countMap = $attributeCountMaps[$attributeName] ?? [];

            $values = collect($attribute['attribute_values']['buckets'])->map(function ($value) use ($countMap) {
                $valueName = $value['key'];
                // Use count from filtered aggregation if available, otherwise 0
                $count = $countMap[$valueName] ?? 0;

                return [
                    'name' => $valueName,
                    'count' => $count,
                ];
            });

            return [
                $attributeName => $values,
            ];
        });

        // Prepare result
        $result = [
            'brands' => $brandData = collect($allBrandBuckets)
                ->map(function ($bucket) use ($brandsWithFiltersMap) {
                    $brandId = $bucket['key'];
                    // Use count from filtered aggregation if available, otherwise 0
                    $count = $brandsWithFiltersMap->get($brandId, 0);

                    return [
                        'brand_id' => $brandId,
                        'brand_name' => $bucket['brand_details']['hits']['hits'][0]['_source']['brand_name'] ?? null,
                        'count' => $count,
                    ];
                }),
            'attributes' => $attributesData,
            'productIds' => $productIds,
            'minPrice' => $minPrice,
            'maxPrice' => $maxPrice,
        ];

        // Only add features to result if they exist
//        if ($this->hasProductFeatures) {
            // Format features with adaptive counts - using the per-feature group filters
            $featuresData = $featureBuckets->mapWithKeys(function ($feature) use ($featureCountMaps) {
                $featureName = $feature['key'];
                $countMap = $featureCountMaps[$featureName] ?? [];

                $values = collect($feature['feature_values']['buckets'])->map(function ($value) use ($countMap) {
                    $valueName = $value['key'];
                    // Use count from filtered aggregation if available, otherwise 0
                    $count = $countMap[$valueName] ?? 0;

                    return [
                        'name' => $valueName,
                        'count' => $count,
                    ];
                });

                return [
                    $featureName => $values,
                ];
            });

            $result['features'] = $featuresData;
//        }

        return $result;
    }

    /**
     * Check if a nested field exists in the Elasticsearch index
     */
//    private function checkIfNestedFieldExists(string $index, string $field): bool
//    {
//        try {
//            // Get the mapping to check if the field exists
//            $mapping = $this->client->indices()->getMapping([
//                'index' => $index,
//            ])->asArray();
//
//            // Check if the field exists and is of type 'nested'
//            return isset($mapping[$index]['mappings']['properties'][$field])
//                && $mapping[$index]['mappings']['properties'][$field]['type'] === 'nested';
//        } catch (\Exception $e) {
//            // If there's an error, assume the field doesn't exist
//            return false;
//        }
//    }

    public function search($index, array $body)
    {
        try {
            return $this->client->search([
                'index' => $index,
                'body' => $body,
            ])->asArray();
        } catch (\Exception $e) {
            // Log the error
            \Log::error('Elasticsearch error: ' . $e->getMessage());
            // Return an empty result
            return [
                'hits' => ['hits' => []],
                'aggregations' => [],
            ];
        }
    }
}

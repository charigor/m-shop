<?php

namespace App\Services\Filter;

use Elastic\Elasticsearch\Client;

class ElasticFilter
{
    public function __construct(private Client $client)
    {
    }

    public function handle($cat_id = null, $filters = [])
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

        //  Add features aggregation
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

        // 1. Get all brands, attributes, and features
        $allMetadataBody = [
            'size' => 0,
            'aggs' => $aggregations,
        ];

        // Add query based on category ID if specified
        $allMetadataBody['query'] = $this->buildQuery($cat_id);

        $allMetadataResponse = $this->search('products_index', $allMetadataBody);
        $allBrandBuckets = $allMetadataResponse['aggregations']['all_brands']['buckets'] ?? [];
        $attributeBuckets = collect($allMetadataResponse['aggregations']['all_attributes']['attributes']['buckets'] ?? []);

        // Only get feature buckets if the field exists
        $featureBuckets = collect($allMetadataResponse['aggregations']['all_features']['features']['buckets'] ?? []);

        // 2. Create filter combinations
        // Only include non-empty filters
        $priceOnlyFilters = !empty($priceFilter) ? [$priceFilter] : [];
        $brandOnlyFilters = !empty($brandFilter) ? [$brandFilter] : [];

        // Create filter set for brands (price + all attribute filters + all feature filters)
        $brandsWithOtherFilters = array_merge($priceOnlyFilters, $attributeFilters, $featureFilters);

        // 3. Get brands with all other filters applied
        $brandsWithFiltersBody = [
            'size' => 0,
            'aggs' => [
                'brands' => [
                    'terms' => [
                        'field' => 'brand_id',
                        'size' => 100,
                    ],
                ],
            ],
        ];

        // Add query with category filter and other filters
        $brandsWithFiltersBody['query'] = $this->buildQuery($cat_id, $brandsWithOtherFilters);

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

            // Add query with category filter and other filters
            $attributeWithFiltersBody['query'] = $this->buildQuery($cat_id, $filtersExcludingCurrentAttribute);

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

            // Add query with category filter and other filters
            $featureWithFiltersBody['query'] = $this->buildQuery($cat_id, $filtersExcludingCurrentFeature);

            $featureWithFiltersResponse = $this->search('products_index', $featureWithFiltersBody);
            $valueBuckets = $featureWithFiltersResponse['aggregations']['filtered_features']['feature_name_filter']['feature_values']['buckets'] ?? [];

            // Create count map for this feature
            $featureCountMaps[$featureName] = collect($valueBuckets)
                ->mapWithKeys(function ($bucket) {
                    return [$bucket['key'] => $bucket['doc_count']];
                })
                ->all();
        }

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
            'aggs' => [
                'unique_products' => [
                    'terms' => [
                        'field' => 'product_id',
                        'size' => 10000, // Set a high value to return all product IDs
                    ],
                ],
                'price_stats' => [
                    'stats' => [
                        'field' => 'price',
                    ],
                ],
                'brand_stats' => [
                    'terms' => [
                        'field' => 'brand_id',
                        'size' => 1000,
                    ],
                ],
            ],
        ];

        // Add query with category filter and all other filters
        $filteredProductsBody['query'] = $this->buildQuery($cat_id, $allFilters);

        $filteredProductsResponse = $this->search('products_index', $filteredProductsBody);

        // Extract product IDs from filtered results
        $productIds = collect($filteredProductsResponse['aggregations']['unique_products']['buckets'] ?? [])
            ->pluck('key')
            ->values()
            ->all();

        // Get price range from filtered products
        $priceStats = $filteredProductsResponse['aggregations']['price_stats'] ?? [];
        $minPrice = $priceStats['min'] ?? null;
        $maxPrice = $priceStats['max'] ?? null;

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
            'brands' => collect($allBrandBuckets)
                ->map(function ($bucket) use ($brandsWithFiltersMap) {
                    $brandId = $bucket['key'];
                    // Get brand count from filtered results
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

        return $result;
    }

    /**
     * Builds Elasticsearch query with category ID and additional filters
     *
     * @param int|null $cat_id Category ID or null if no category specified
     * @param array $filters Additional filters (if any)
     * @return array Elasticsearch query structure
     */
    private function buildQuery($cat_id, array $filters = [])
    {
        // If category is specified
        if ($cat_id !== null) {
            // Base query with category
            $query = [
                'bool' => [
                    'must' => [
                        [
                            'term' => [
                                'category_ids' => $cat_id, // Changed from category_id to category_ids
                            ],
                        ],
                    ],
                ],
            ];

            // Add additional filters if they exist
            if (!empty($filters)) {
                $query['bool']['filter'] = $filters;
            }

            return $query;
        }

        // If no category but has filters
        if (!empty($filters)) {
            return [
                'bool' => [
                    'filter' => $filters,
                ],
            ];
        }

        // If no category and no filters - return match_all
        return [
            'match_all' => new \stdClass(),
        ];
    }

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
            \Log::error('Query: ' . json_encode($body));

            // Return an empty result
            return [
                'hits' => ['hits' => []],
                'aggregations' => [],
            ];
        }
    }
}

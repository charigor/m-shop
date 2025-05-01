<?php

namespace App\Services\Filter\Elastic;

use Elastic\Elasticsearch\Client;
use Illuminate\Support\Collection;

class Filter
{
    private string $indexName = 'products_index';

    public function __construct(private Client $client)
    {
    }

    public function handle($cat_id = null, $filters = [])
    {
        // Process filters
        $processedFilters = $this->processFilters($filters);

        // Create Elasticsearch filters
        $elasticFilters = $this->createElasticFilters($processedFilters);

        // Get all metadata (brands, attributes, features)
        $metadataResponse = $this->getMetadata($cat_id);

        // Get facet counts considering applied filters
        $facetCounts = $this->getFacetCounts($cat_id, $elasticFilters);

        // Get filtered products
        $filteredProducts = $this->getFilteredProducts($cat_id, $elasticFilters['allFilters']);

        // Format results
        return $this->formatResults($metadataResponse, $facetCounts, $filteredProducts);
    }

    /**
     * Preprocess incoming filters
     */
    private function processFilters(array $filters): array
    {
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

        // Process string values in features
        foreach ($features as $key => $value) {
            if (is_string($value)) {
                $features[$key] = explode(',', $value);
            }
        }

        return [
            'brands' => $brands,
            'price' => $price,
            'attributes' => $attributes,
            'features' => $features
        ];
    }

    /**
     * Create Elasticsearch filters
     */
    private function createElasticFilters(array $processedFilters): array
    {
        // Create price filter
        $priceFilter = $this->createPriceFilter($processedFilters['price']);

        // Create brand filter
        $brandFilter = $this->createBrandFilter($processedFilters['brands']);

        // Create attribute filters
        list($attributeFilters, $attributeGroupFilters) = $this->createAttributeFilters($processedFilters['attributes']);

        // Create feature filters
        list($featureFilters, $featureGroupFilters) = $this->createFeatureFilters($processedFilters['features']);

        // Create filter combinations
        $priceOnlyFilters = !empty($priceFilter) ? [$priceFilter] : [];
        $brandOnlyFilters = !empty($brandFilter) ? [$brandFilter] : [];

        // All filters combined
        $allFilters = array_merge(
            $priceOnlyFilters,
            $brandOnlyFilters,
            $attributeFilters,
            $featureFilters
        );

        return [
            'priceFilter' => $priceFilter,
            'brandFilter' => $brandFilter,
            'attributeFilters' => $attributeFilters,
            'attributeGroupFilters' => $attributeGroupFilters,
            'featureFilters' => $featureFilters,
            'featureGroupFilters' => $featureGroupFilters,
            'priceOnlyFilters' => $priceOnlyFilters,
            'brandOnlyFilters' => $brandOnlyFilters,
            'allFilters' => $allFilters
        ];
    }

    /**
     * Create price filter
     */
    private function createPriceFilter(array $price): array
    {
        if (empty($price)) {
            return [];
        }

        $range = [];
        foreach ($price as $part) {
            if (str_starts_with($part, 'min')) {
                $range['gte'] = (int)str_replace('min', '', $part);
            }
            if (str_starts_with($part, 'max')) {
                $range['lte'] = (int)str_replace('max', '', $part);
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

    /**
     * Create brand filter
     */
    private function createBrandFilter(array $brands): array
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

    /**
     * Create attribute filters
     */
    private function createAttributeFilters(array $attributes): array
    {
        $attributeFilters = [];
        $attributeGroupFilters = [];

        if (empty($attributes)) {
            return [$attributeFilters, $attributeGroupFilters];
        }

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

        return [$attributeFilters, $attributeGroupFilters];
    }

    /**
     * Create feature filters
     */
    private function createFeatureFilters(array $features): array
    {
        $featureFilters = [];
        $featureGroupFilters = [];

        if (empty($features)) {
            return [$featureFilters, $featureGroupFilters];
        }

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

        return [$featureFilters, $featureGroupFilters];
    }

    /**
     * Get all metadata
     */
    private function getMetadata($cat_id): array
    {
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
            'all_features' => [
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
            ],
        ];

        $body = [
            'size' => 0,
            'aggs' => $aggregations,
        ];

        $body['query'] = $this->buildQuery($cat_id);

        return $this->search($this->indexName, $body);
    }

    /**
     * Get filtered products
     */
    private function getFilteredProducts($cat_id, array $allFilters): array
    {
        $body = [
            'size' => 0,
            'aggs' => [
                'unique_products' => [
                    'terms' => [
                        'field' => 'product_id',
                        'size' => 10000,
                    ],
                ],
                'price_stats' => [
                    'stats' => [
                        'field' => 'price',
                    ],
                ],
            ],
        ];

        $body['query'] = $this->buildQuery($cat_id, $allFilters);

        return $this->search($this->indexName, $body);
    }

    /**
     * Get facet counts
     */
    private function getFacetCounts($cat_id, array $elasticFilters): array
    {
        return [
            'brands' => $this->getBrandCounts($cat_id, $elasticFilters),
            'attributes' => $this->getAttributeCounts($cat_id, $elasticFilters),
            'features' => $this->getFeatureCounts($cat_id, $elasticFilters),
        ];
    }

    /**
     * Get brand counts
     */
    private function getBrandCounts($cat_id, array $elasticFilters): array
    {
        // For brands, we exclude only brand filters while keeping all other filters
        $filtersExcludingBrands = array_merge(
            $elasticFilters['priceOnlyFilters'],
            $elasticFilters['attributeFilters'],
            $elasticFilters['featureFilters']
        );

        $body = [
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

        $body['query'] = $this->buildQuery($cat_id, $filtersExcludingBrands);
        $response = $this->search($this->indexName, $body);

        return collect($response['aggregations']['brands']['buckets'] ?? [])
            ->mapWithKeys(function ($bucket) {
                return [$bucket['key'] => $bucket['doc_count']];
            })->all();
    }

    /**
     * Get attribute counts
     */
    private function getAttributeCounts($cat_id, array $elasticFilters): array
    {
        $attributeCountMaps = [];
        $attributeBuckets = collect($this->getMetadata($cat_id)['aggregations']['all_attributes']['attributes']['buckets'] ?? []);

        foreach ($attributeBuckets as $attributeBucket) {
            $attributeName = $attributeBucket['key'];

            // Create filters excluding the current attribute group
            $filtersExcludingCurrentAttribute = array_merge(
                $elasticFilters['priceOnlyFilters'],
                $elasticFilters['brandOnlyFilters'],
                $elasticFilters['featureFilters'] // Include all feature filters
            );

            // Add filters for all other attribute groups
            foreach ($elasticFilters['attributeGroupFilters'] as $attrName => $filter) {
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

            $attributeWithFiltersBody['query'] = $this->buildQuery($cat_id, $filtersExcludingCurrentAttribute);
            $attributeWithFiltersResponse = $this->search($this->indexName, $attributeWithFiltersBody);
            $valueBuckets = $attributeWithFiltersResponse['aggregations']['filtered_attributes']['attribute_name_filter']['attribute_values']['buckets'] ?? [];

            // Create count map for this attribute
            $attributeCountMaps[$attributeName] = collect($valueBuckets)
                ->mapWithKeys(function ($bucket) {
                    return [$bucket['key'] => $bucket['doc_count']];
                })
                ->all();
        }

        return $attributeCountMaps;
    }

    /**
     * Get feature counts
     */
    private function getFeatureCounts($cat_id, array $elasticFilters): array
    {
        $featureCountMaps = [];
        $featureBuckets = collect($this->getMetadata($cat_id)['aggregations']['all_features']['features']['buckets'] ?? []);

        foreach ($featureBuckets as $featureBucket) {
            $featureName = $featureBucket['key'];

            // Create filters excluding the current feature group
            $filtersExcludingCurrentFeature = array_merge(
                $elasticFilters['priceOnlyFilters'],
                $elasticFilters['brandOnlyFilters'],
                $elasticFilters['attributeFilters'] // Include all attribute filters
            );

            // Add filters for all other feature groups
            foreach ($elasticFilters['featureGroupFilters'] as $featName => $filter) {
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

            $featureWithFiltersBody['query'] = $this->buildQuery($cat_id, $filtersExcludingCurrentFeature);
            $featureWithFiltersResponse = $this->search($this->indexName, $featureWithFiltersBody);
            $valueBuckets = $featureWithFiltersResponse['aggregations']['filtered_features']['feature_name_filter']['feature_values']['buckets'] ?? [];

            // Create count map for this feature
            $featureCountMaps[$featureName] = collect($valueBuckets)
                ->mapWithKeys(function ($bucket) {
                    return [$bucket['key'] => $bucket['doc_count']];
                })
                ->all();
        }

        return $featureCountMaps;
    }

    /**
     * Format results
     */
    private function formatResults(array $metadataResponse, array $facetCounts, array $filteredProducts): array
    {
        // Extract product IDs
        $productIds = collect($filteredProducts['aggregations']['unique_products']['buckets'] ?? [])
            ->pluck('key')
            ->values()
            ->all();

        // Extract price range
        $priceStats = $filteredProducts['aggregations']['price_stats'] ?? [];
        $minPrice = $priceStats['min'] ?? null;
        $maxPrice = $priceStats['max'] ?? null;

        // Format brands
        $brands = $this->formatBrands(
            $metadataResponse['aggregations']['all_brands']['buckets'] ?? [],
            $facetCounts['brands']
        );

        // Format attributes
        $attributes = $this->formatAttributes(
            collect($metadataResponse['aggregations']['all_attributes']['attributes']['buckets'] ?? []),
            $facetCounts['attributes']
        );

        // Format features
        $features = $this->formatFeatures(
            collect($metadataResponse['aggregations']['all_features']['features']['buckets'] ?? []),
            $facetCounts['features']
        );

        return [
            'brands' => $brands,
            'attributes' => $attributes,
            'features' => $features,
            'productIds' => $productIds,
            'minPrice' => $minPrice,
            'maxPrice' => $maxPrice,
        ];
    }

    /**
     * Format brands with counts
     */
    private function formatBrands(array $brandBuckets, array $brandCountMap): Collection
    {
        return collect($brandBuckets)
            ->map(function ($bucket) use ($brandCountMap) {
                $brandId = $bucket['key'];
                $count = $brandCountMap[$brandId] ?? 0;

                return [
                    'brand_id' => $brandId,
                    'brand_name' => $bucket['brand_details']['hits']['hits'][0]['_source']['brand_name'] ?? null,
                    'count' => $count,
                ];
            });
    }

    /**
     * Format attributes with counts
     */
    private function formatAttributes(Collection $attributeBuckets, array $attributeCountMaps): Collection
    {
        return $attributeBuckets->mapWithKeys(function ($attribute) use ($attributeCountMaps) {
            $attributeName = $attribute['key'];
            $countMap = $attributeCountMaps[$attributeName] ?? [];

            $values = collect($attribute['attribute_values']['buckets'])->map(function ($value) use ($countMap) {
                $valueName = $value['key'];
                $count = $countMap[$valueName] ?? 0;

                return [
                    'name' => $valueName,
                    'count' => $count,
                ];
            });

            return [$attributeName => $values];
        });
    }

    /**
     * Format features with counts
     */
    private function formatFeatures(Collection $featureBuckets, array $featureCountMaps): Collection
    {
        return $featureBuckets->mapWithKeys(function ($feature) use ($featureCountMaps) {
            $featureName = $feature['key'];
            $countMap = $featureCountMaps[$featureName] ?? [];

            $values = collect($feature['feature_values']['buckets'])->map(function ($value) use ($countMap) {
                $valueName = $value['key'];
                $count = $countMap[$valueName] ?? 0;

                return [
                    'name' => $valueName,
                    'count' => $count,
                ];
            });

            return [$featureName => $values];
        });
    }

    /**
     * Build query with category ID and additional filters
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
                                'category_ids' => $cat_id,
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

    /**
     * Execute query to Elasticsearch
     */
    private function search($index, array $body)
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

            // Return empty result
            return [
                'hits' => ['hits' => []],
                'aggregations' => [],
            ];
        }
    }
}

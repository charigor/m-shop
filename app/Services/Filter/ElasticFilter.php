<?php

namespace App\Services\Filter;

use Elastic\Elasticsearch\Client;

class ElasticFilter
{
    public function __construct(private Client $client)
    {
    }

    public function handle($cat_id, $filters = [])
    {

        $brands = isset($filters['brands']) && !empty($filters['brands']) ? explode(',', $filters['brands']) : [];
        $price = isset($filters['price']) && !empty($filters['price']) ? explode(',', $filters['price']) : [];

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

        // 1. Get all brands in category (no filters applied)
        $allBrandsBody = [
            'size' => 0,
            'query' => [
                'term' => [
                    'category_id' => $cat_id,
                ],
            ],
            'aggs' => [
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
            ],
        ];
        $allBrandsResponse = $this->search('products_index', $allBrandsBody);
        $allBrandBuckets = $allBrandsResponse['aggregations']['all_brands']['buckets'] ?? [];

        // 2. Get brands with price filter only (no brand filter)
        $priceFilteredBody = [
            'size' => 0,
            'query' => [
                'bool' => [
                    'must' => [
                        ['term' => ['category_id' => $cat_id]],
                    ],
                    'filter' => !empty($priceFilter) ? [$priceFilter] : [],
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
        $priceFilteredResponse = $this->search('products_index', $priceFilteredBody);
        $priceFilteredBrands = $priceFilteredResponse['aggregations']['brands']['buckets'] ?? [];
        $priceFilteredBrandMap = collect($priceFilteredBrands)->mapWithKeys(function ($bucket) {
            return [$bucket['key'] => $bucket['doc_count']];
        });

        // 3. Get filtered products (apply all filters)
        $filteredProductsBody = [
            'size' => 0,
            'query' => [
                'bool' => [
                    'must' => [
                        ['term' => ['category_id' => $cat_id]],
                    ],
                    'filter' => array_filter([
                        !empty($priceFilter) ? $priceFilter : null,
                        !empty($brandFilter) ? $brandFilter : null,
                    ]),
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
        $filteredProductsResponse = $this->search('products_index', $filteredProductsBody);
        $filteredBuckets = $filteredProductsResponse['aggregations']['brands']['buckets'] ?? [];

        // Extract product IDs from filtered results
        $productIds = collect($filteredBuckets)->flatMap(function ($bucket) {
            return collect($bucket['products']['hits']['hits'] ?? [])
                ->map(function ($hit) {
                    return $hit['_source']['product_id'];
                });
        })->unique()->values()->all();

        // Calculate price range
        $minPrice = collect($filteredBuckets)->pluck('min_price.value')->filter()->min();
        $maxPrice = collect($filteredBuckets)->pluck('max_price.value')->filter()->max();

        // Map all brands with their counts from price filter
        $brandData = collect($allBrandBuckets)
            ->map(function ($bucket) use ($priceFilteredBrandMap) {
                $brandId = $bucket['key'];
                $count = $priceFilteredBrandMap->get($brandId, 0);

                return [
                    'brand_id' => $brandId,
                    'brand_name' => $bucket['brand_details']['hits']['hits'][0]['_source']['brand_name'] ?? null,
                    'count' => $count,
                ];
            });

        return [
            'brands' => $brandData,
            'productIds' => $productIds,
            'minPrice' => $minPrice,
            'maxPrice' => $maxPrice,
        ];
    }

    public function search($index, array $body)
    {
        return $this->client->search([
            'index' => $index,
            'body' => $body,
        ])->asArray();
    }
}

<?php

namespace App\Services\Filter;

use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;

class ElasticFilter
{
    public function __construct(private Client $client)
    {
    }

    public function handle($cat_id, $filters = [])
    {
        $body = [
            'query' => [
                'match' => [
                    'category_id' => $cat_id
                ]
            ],
            'aggs' => [
                'brands' => [
                    'terms' => [
                        'field' => 'brand_id',
                        'size' => 100
                    ],
                    'aggs' => [
                        'brand_name_sample' => [
                            'top_hits' => [
                                'size' => 1,
                                '_source' => ['brand_name']
                            ]
                        ]
                    ]
                ]
            ]

        ];

        $response = $this->search('products_index', $body);

        $buckets = $response['aggregations']['brands']['buckets'] ?? [];
        $productIds = collect($response['hits']['hits'] ?? [])
            ->pluck('_source.product_id') // Assuming the product_id is stored under _source
            ->all();
        return ['brands' => collect($buckets)->map(function ($bucket) {
            return [
                'brand_id' => $bucket['key'],
                'brand_name' => $bucket['brand_name_sample']['hits']['hits'][0]['_source']['brand_name'] ?? null,
                'count' => $bucket['doc_count']
            ];
        })];
    }

    public function search($index, array $body)
    {
        return $this->client->search([
            'index' => $index,
            'body' => $body,
        ])->asArray();
    }
}

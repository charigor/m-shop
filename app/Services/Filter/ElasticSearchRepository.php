<?php

namespace App\Services\Filter;

use App\Models\Brand;
use App\Services\Filter\BrandSearchRepository;
use App\Services\Filter\SearchRepository;
use Elastic\Elasticsearch\Client;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;

class ElasticSearchRepository implements BrandSearchRepository
{

    private Client $elasticsearch;

    public function __construct(Client $elasticsearch)
    {
        $this->elasticsearch = $elasticsearch;
    }

    public function search(string $query = ''): Collection
    {
        $items = $this->searchOnElasticsearch($query);

        return $this->buildCollection($items);
    }

    private function searchOnElasticsearch(string $query = '')
    {
        $model = new Brand;

        $items = $this->elasticsearch->search([
            'index' => $model->getSearchIndex(),
            'type' => $model->getSearchType(),
            "tokenizer" => "ngram",
            'body' => [
                'query' => [
                    'multi_match' => [
                        'fields' => ['name^1'],
                        'type' => 'phrase_prefix',
                        'query' => $query,
                    ],
                ],
//                'from' => 0,
//                'size' => 2,
                'highlight' => [
                    'fields' => [
                        'name' => new \stdClass()
                    ]
                ]
            ],
        ]);
        return $items;
    }

    private function buildCollection($items): Collection
    {
        info($items);
        $ids = Arr::pluck($items['hits']['hits'], '_id');

        return Brand::findMany($ids)
            ->sortBy(function ($article) use ($ids) {
                return array_search($article->getKey(), $ids);
            });
    }
}

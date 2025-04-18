<?php

namespace App\Services\Search;

use App\Models\Product;
use App\Services\Contracts\SearchEngineInterface;
use Elastic\Elasticsearch\Client;
use Illuminate\Http\Request;

class ElasticSearch implements SearchEngineInterface
{

    public function __construct(private Client $client) {

    }
    public function handle(Request $request): array
    {
        $query = $request->input('q');
        $lang = $request->input('lang', app()->getLocale());
        $categoryId = $request->input('id');
        $page = (int) $request->input('page', 1);
        $perPage = 12;



        $searchResultCat = $this->getFullCategorySearchResult( $query, $lang);
        $totalProducts = $this->getTotalProductsCount($searchResultCat);
        $categories = $this->formatCategories($searchResultCat, $lang);

        $itemGroups = $categoryId
            ? $this->getCategoryItemGroup($query, $lang, $categoryId, $page, $perPage)
            : $this->getGroupedItems($searchResultCat, $lang);

        return [
            'itemGroups' => $itemGroups,
            'categories' => $categories,
            'total' => $totalProducts
        ];
    }
    public function search($index, array $body)
    {
        return $this->client->search([
            'index' => $index,
            'body' => $body,
        ])->asArray();
    }

    private function getFullCategorySearchResult($query, $lang): array
    {
        return $this->search( 'category_product_lang', [
            '_source' => ["product_name.$lang", "category_title.$lang", "product_id", "category_id"],
            'query' => [
                'multi_match' => [
                    'query' => $query,
                    'fields' => ['product_name.uk^3', 'product_name.en^3'],
                    'type' => 'best_fields',
                    'fuzziness' => 'AUTO'
                ]
            ],
            'aggs' => [
                'categories' => [
                    'terms' => ['field' => 'category_id', 'size' => 1000],
                    'aggs' => [
                        'category_title' => ['top_hits' => ['size' => 1, '_source' => ["category_title.$lang"]]],
                        'total_products' => ['value_count' => ['field' => 'product_id']],
                        'products' => ['top_hits' => ['size' => 4, '_source' => ["product_name.$lang", "product_id", "category_title.$lang"]]]
                    ]
                ]
            ]
        ]);
    }

    private function getTotalProductsCount($searchResultCat)
    {
        return collect($searchResultCat['aggregations']['categories']['buckets'])->sum(fn($bucket) => $bucket['total_products']['value'] ?? 0);
    }

    private function formatCategories($searchResultCat, $lang): \Illuminate\Support\Collection
    {
        return collect($searchResultCat['aggregations']['categories']['buckets'])->map(fn($bucket) => [
            'id' => $bucket['key'],
            'count' => $bucket['doc_count'],
            'name' => $bucket['category_title']['hits']['hits'][0]['_source']['category_title'][$lang] ?? 'Без назви',
        ]);
    }

    private function getGroupedItems($searchResultCat, $lang): \Illuminate\Support\Collection
    {
        return collect($searchResultCat['aggregations']['categories']['buckets'])->map(function ($bucket) use ($lang) {
            $categoryTitle = $bucket['category_title']['hits']['hits'][0]['_source']['category_title'][$lang] ?? 'Без назви';
            $products = collect($bucket['products']['hits']['hits'])->map(function ($hit) use ($lang) {
                $product = Product::find($hit['_source']['product_id']);
                return [
                    'product_id' => $hit['_source']['product_id'],
                    'name' => $hit['_source']['product_name'][$lang] ?? 'Без назви',
                    'image' => optional($product->mainImage)->getUrl('preview'),
                    'price' => (int)$product->price,
                ];
            });

            return [
                'category' => [
                    'name' => $categoryTitle,
                    'id' => $bucket['key'],
                    'count' => $bucket['doc_count'],
                ],
                'items' => $products,
            ];
        });
    }

    private function getCategoryItemGroup($query, $lang, $categoryId, $page, $perPage): array
    {
        $from = ($page - 1) * $perPage;

        $searchResult = $this->search( 'category_product_lang', [
            '_source' => ["product_name.$lang", "category_title.$lang", "product_id", "category_id"],
            'query' => [
                'bool' => [
                    'must' => [
                        ['term' => ['category_id' => $categoryId]],
                        ['multi_match' => [
                            'query' => $query,
                            'fields' => ['product_name.uk^3', 'product_name.en^3'],
                            'type' => 'best_fields',
                            'fuzziness' => 'AUTO'
                        ]]
                    ]
                ]
            ],
            'from' => $from,
            'size' => $perPage,
            'aggs' => [
                'categories' => [
                    'terms' => ['field' => 'category_id', 'size' => 1],
                    'aggs' => [
                        'category_title' => ['top_hits' => ['size' => 1, '_source' => ["category_title.$lang"]]]
                    ]
                ]
            ]
        ]);

        return [[
            'category' => [
                'name' => $searchResult['aggregations']['categories']['buckets'][0]['category_title']['hits']['hits'][0]['_source']['category_title'][$lang] ?? 'Без назви',
                'id' => $searchResult['aggregations']['categories']['buckets'][0]['key'],
                'count' => $searchResult['hits']['total']['value'],
            ],
            'items' => collect($searchResult['hits']['hits'])->map(function ($item) use ($lang) {
                $product = Product::find($item['_source']['product_id']);
                return [
                    'product_id' => $item['_source']['product_id'],
                    'name' => $item['_source']['product_name'][$lang] ?? 'Без назви',
                    'image' => optional($product->mainImage)->getUrl('preview'),
                    'price' => (int)$product->price,
                ];
            })
        ]];
    }
}

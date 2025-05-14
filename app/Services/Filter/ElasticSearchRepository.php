<?php

namespace App\Services\Filter;

use App\Models\Product;
use App\Models\ProductLang;
use Elastic\Elasticsearch\Client;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;

class ElasticSearchRepository implements BrandSearchRepository
{
    private Client $elasticsearch;

    public object $product;

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
        $model = new ProductLang;

        $items = $this->elasticsearch->search([
            'index' => $model->getSearchIndex(),
            'type' => $model->getSearchType(),
            'tokenizer' => 'ngram',
            'body' => [
                'query' => [
                    'multi_match' => [
                        'query' => $query,
                        'fields' => ['name', 'description'],
                        'type' => 'phrase_prefix',
                    ],

                    //                    'multi_match' => [
                    //                        'fields' => ['name^1'],
                    //                        'type' => 'phrase_prefix',
                    //                        'query' => $query,
                    //
                    //                    ],

                ],
                //                'from' => 0,
                //                'size' => 2,

                'highlight' => [
                    'fields' => [
                        'name' => new \stdClass,
                    ],
                ],
            ],
        ]);

        return $items;
    }

    private function buildCollection($items): Collection
    {
        $ids = Arr::pluck($items['hits']['hits'], '_id');
        //        dd(Product::with('translation')->whereHas('translation',fn($q) => $q->whereIn('id',$ids)->where('locale',app()->getLocale()))->get());

        return Product::query()->selectRaw('product_lang.id, product_lang.product_id, product_lang.name, product_lang.description')
            ->leftJoin('product_lang', 'product_lang.product_id', '=', 'products.id')
            ->whereIn('product_lang.id', $ids)->get()
            ->sortBy(function ($article) use ($ids) {
                return array_search($article->getKey(), $ids);
            });
        //        return Product::with('translation')->whereHas('translation',fn($q) => $q->whereIn('id',$ids)->where('locale',app()->getLocale()))->get()
        //            ->sortBy(function ($article) use ($ids) {
        //                return array_search($article->getKey(), $ids);
        //            });
    }
}

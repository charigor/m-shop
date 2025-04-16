<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Elastic\Elasticsearch\ClientBuilder;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $client = ClientBuilder::create()
            ->setHosts([env('ELASTICSEARCH_HOST', 'http://elasticsearch:9200')])
            ->build();

        $query = $request->input('q');
        $lang = $request->input('lang', app()->getLocale());
        if (!$query) {
            return response()->json([], 400);
        }
        $params = [
            'index' => 'category_lang',
            'body' => [
                'query' => [
                    'match' => [
                        'title' => [
                            'query' => $query,
                        ],
                    ],
                ],
                'collapse' => [
                    'field' => 'category_id' // Группируем по категории
                ],
                'sort' => [
                    '_score' => 'desc'
                ],
                'size' => 10
            ],
        ];



        try {
            $results = $client->search($params);
            $hits = $results['hits']['hits'];
            if (count($hits) === 0) {
                return response()->json([]);
            }
            $categoryIds = collect($hits)->pluck('_source.category_id')->all();

            $params = [
                'index' => 'category_lang',
                'body' => [
                    'query' => [
                        'bool' => [
                            'filter' => [
                                ['terms' => ['category_id' => $categoryIds]],
                                ['term' => ['locale' => $lang]],
                            ],
                        ],
                    ],
                    'size' => count($categoryIds),
                ],
            ];

            $localizedResult = $client->search($params);
            return response()->json($localizedResult['hits']['hits']);

        } catch (\Exception $e) {
            \Log::error('Elasticsearch error: '.$e->getMessage());
            return response()->json(['error' => 'Search failed'], 500);
        }
    }

}

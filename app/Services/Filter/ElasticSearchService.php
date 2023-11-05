<?php

namespace App\Services\Filter;

use App\Models\Feature;
use App\Models\Product;
use Elastic\Elasticsearch\Client;
use Exception;
use Illuminate\Http\Request;

class ElasticSearchService
{
    protected Product $product;

    public function indexProduct(int $id)
    {
        $this->product = Product::find($id);
        $this->product->load(['featureValues.feature', 'categories']);

        $filters = $this->product->featureValues()->get()->map(function ($value) {
            return [
                'name' => $value->feature->guard_name,
                'pretty_name' => $value->feature->translate->name,
                'value' => $value->translate->value,
            ];
        })->toArray();
        $filters[] = [
            'name' => 'type',
            'pretty_name' => 'type',
            'value' => 'product',
        ];
        $filters[] = [
            'name' => 'brand',
            'pretty_name' => 'brand',
            'value' => $this->product->brand->name,
        ];
        $this->product->categories()->get()->map(function ($value) use (&$filters) {
            $filters[] = [
                'name' => 'category',
                'pretty_name' => 'category',
                'value' => $value->translate->link_rewrite,
            ];
        })->toArray();
        $document = [
            'index' => 'mshop',
            'type' => 'nested',
            'id' => $this->product->id,
            'body' => [
                'title' => $this->product->translate->name,
                'description' => $this->product->translate->description,
                'price' => $this->product->price,
                'active' => $this->product->active,
                'reference' => $this->product->reference,
                'quantity' => $this->product->quantity,
                'filters' => $filters,
            ],
        ];

        if (count($filters) === 0) {
            unset($document['body']['filters']);
        }

        try {
            return $this->getElasticSearchClient()->index($document);

            if (in_array($results['result'], ['created', 'updated'])) {
                return response()->api(
                    is_success: true,
                    message: 'Product indexed successfully with status:'.$results['result'],
                    code: 200
                );
            }
        } catch (Exception $e) {
            return response()->api(
                is_success: false,
                message: 'Product indexed failed',
                code: 500,
                data: $e->getMessage()
            );
        }
    }

    public function getElasticSearchClient(): Client
    {
        return \Elastic\Elasticsearch\ClientBuilder::create()->setSSLVerification(false)
            ->setHosts(['http://elastic:9200'])
            ->build();
    }

    public function search(Request $request)
    {
        $requestFilterValues = Feature::all()->pluck('guard_name')->toArray();

        array_push($requestFilterValues, 'brand', 'category', 'type');

        $requestFilters = $request->only($requestFilterValues) ?? [];
        //        $params = [
        //            'index' => 'mshop',
        //            'type' => 'nested',
        //            'size' => 25,
        //            'body' => [
        //
        //                 "aggs" =>  [
        //                     "aggs_all_filters" => []
        //                 ]
        //                     "filter" => [
        //                         "nested" => [
        //                             "path" => "filters",
        //                             "filter" => [
        //                                 "bool" => [
        //                                     "must" => [
        //                                         [
        //                                             "term" => [
        //                                                 "filters.name" => "brand"
        //                                             ]
        //                                         ],
        //                                         "terms" => [
        //                                             "filters.value" => [
        //                                                 "Imogene Miller"
        //                                             ]
        //                                         ]
        //                                     ]
        //                                 ]
        //                             ]
        //                         ]
        //                     ],
        //                    "aggs_all_filters" => [
        //                        "nested" => [
        //                            "path" => "filters"
        //                        ],
        //                        "aggs" => [
        //                            "name" => [
        //                                "terms" => [
        //                                    "field" => "filters.name"
        //                                ]
        //                             ] ,
        //                            "aggs" => [
        //                                "value" => [
        //                                    "terms" => [
        //                                        "field" => "filters.value"
        //                                    ]
        //                                ]
        //                            ]
        //                        ],
        //
        //                    ]
        //                ]

        //                'aggs' => [
        //                    'aggs_all_filters' => [
        //                        'filter' => [
        //                            'bool' => [
        //                                'filter' => [
        //                                    [
        //                                        'multi_match' => [
        //                                            'query' => request()->get('term') ?? '',
        //                                            'fields' => ['title','description','all_filters'],
        //                                            'operator' => 'and'
        //                                        ]
        //                                    ]
        //                                ]
        //                            ]
        //                        ],
        //                        'aggs' => [
        //                            'facets' => [
        //                                'nested' => [
        //                                    'path' => 'filters'
        //                                ],
        //                                'aggs' => [
        //                                    'names' => [
        //                                        'terms' => [
        //                                            'field' => 'filters.name'
        //                                        ],
        //                                        'aggs' => [
        //                                            'values' => [
        //                                                'terms' => [
        //                                                    'field' => 'filters.value',
        //                                                    'order' => [
        //                                                        '_key' => 'asc'
        //                                                    ]
        //                                                ]
        //                                            ]
        //                                        ]
        //                                    ]
        //                                ]
        //                            ]
        //                        ]
        //                    ]
        //                ],
        //                'post_filter' => [
        //                    'bool' => [
        //                        'filter' => [
        //                            [
        //                                'nested' => [
        //                                    'path' => 'filters',
        //                                    'query' =>  [
        //                                        'bool' => [
        //                                            'must' => [
        //                                                [
        //                                                    'term' => [
        //                                                        'filters.name' => request()->get('term') ?? '',
        //                                                    ]
        //                                                ]
        //                                            ]
        //                                        ]
        //                                    ]
        //                                ],
        //                                'multi_match' => [
        //                                    'query' => request()->get('term') ?? '',
        //                                    'fields' => ['title','description','all_filters'],
        //                                    'operator' => 'and'
        //                                ]
        //                            ]
        //                        ]
        //                    ]
        //                ],
        //            ]
        //        ];
        //**********************************************************************************************************************************
        $params = [
            'index' => 'mshop',
            'type' => 'nested',
            'size' => 25,
            'body' => [
                'aggs' => [
                    'aggs_all_filters' => [
                        'filter' => [
                            'bool' => [
                                'filter' => [
                                    [
                                        'multi_match' => [
                                            'query' => request()->get('term') ?? '',
                                            'fields' => ['title', 'description', 'all_filters'],
                                            'operator' => 'and',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        'aggs' => [
                            'facets' => [
                                'nested' => [
                                    'path' => 'filters',
                                ],
                                'aggs' => [
                                    'names' => [
                                        'terms' => [
                                            'field' => 'filters.name',
                                        ],
                                        'aggs' => [
                                            'values' => [
                                                'terms' => [
                                                    'field' => 'filters.value',
                                                    'order' => [
                                                        '_key' => 'asc',
                                                    ],
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                //                'post_filter' => [
                //                    'bool' => [
                //                        'filter' => [
                //                            [
                //                                'nested' => [
                //                                    'path' => 'filters',
                //                                    'query' =>  [
                //                                        'bool' => [
                //                                            'must' => [
                //                                                [
                //                                                    'term' => [
                //                                                        'filters.name' => request()->get('term') ?? '',
                //                                                    ]
                //                                                ]
                //                                            ]
                //                                        ]
                //                                    ]
                //                                ],
                //                                'multi_match' => [
                //                                    'query' => request()->get('term') ?? '',
                //                                    'fields' => ['title','description','all_filters'],
                //                                    'operator' => 'and'
                //                                ]
                //                            ]
                //                        ]
                //                    ]
                //                ],
            ],
        ];
        if (request()->has('term') && ! empty(request()->get('term'))) {
            $params['body']['query'] = [
                'bool' => [
                    'must' => [
                        [
                            'multi_search' => [
                                'query' => request()->get('term'),
                                'fields' => ['title', 'description', 'all_filters'],
                                'operator' => 'and',
                            ],
                        ],
                    ],
                ],
            ];
        }
        if (count($requestFilters) > 0) {
            $requestFilters = array_filter($requestFilters, function ($value) {
                return ! empty($value);
            });

            if (count($requestFilters) > 0) {
                $aggsFilters = [];
                foreach ($requestFilters as $key => $values) {
                    if (count($requestFilters) > 1) {
                        $diff = array_values(array_diff(array_keys($requestFilters), [$key]));

                        $aggsFilters[$key] = $diff;

                    }
                    if (count($requestFilters) === 1) {
                        $aggsFilters[$key][] = $key;
                    }
                }

                foreach ($aggsFilters as $key => $innerFacets) {

                    foreach ($innerFacets as $innerKey => $filterKey) {
                        $aggsFilters[$key][$filterKey] = explode(',', $requestFilters[$filterKey]);
                        unset($aggsFilters[$key][$innerKey]);
                    }
                }

                if (count($aggsFilters) > 0) {
                    foreach ($aggsFilters as $outerAggKey => $innerAggs) {
                        foreach ($innerAggs as $key => $values) {

                            $addFilterNameAndValues = [
                                [
                                    'term' => [
                                        'filters.name' => $key,
                                    ],
                                ],
                            ];
                            foreach ($values as $value) {

                                $addFilterNameAndValues[1] = [
                                    'bool' => [
                                        'must' => [
                                            [
                                                'term' => [
                                                    'filters.value' => $value,
                                                ],
                                            ],
                                        ],
                                    ],
                                ];
                            }
                            $params['body']['aggs']['aggs_'.$outerAggKey] = [
                                'filter' => [
                                    'bool' => [
                                        'filter' => [
                                            [
                                                'nested' => [
                                                    'path' => 'filters',
                                                    'query' => [
                                                        'bool' => [
                                                            'filter' => $addFilterNameAndValues,
                                                        ],
                                                    ],
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                                'aggs' => [
                                    'facets' => [
                                        'nested' => [
                                            'path' => 'filters',
                                        ],
                                        'aggs' => [
                                            'aggs_special' => [
                                                'filter' => [
                                                    'match' => [
                                                        'filters.name' => $outerAggKey,

                                                    ],
                                                ],
                                                'aggs' => [
                                                    'names' => [
                                                        'terms' => [
                                                            'field' => 'filters.name',
                                                        ],
                                                        'aggs' => [
                                                            'values' => [
                                                                'terms' => [
                                                                    'field' => 'filters.value',
                                                                    'order' => [
                                                                        '_key' => 'asc',
                                                                    ],
                                                                ],
                                                            ],
                                                        ],
                                                    ],
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ];
                        }
                    }
                }
                $i = 0;

                foreach ($requestFilters as $filter => $values) {
                    $params['body']['aggs']['aggs_all_filters']['filter']['bool']['filter'][$i] = [
                        'nested' => [
                            'path' => 'filters',
                            'query' => [
                                'bool' => [
                                    'must' => [
                                        [
                                            'term' => [
                                                'filters.name' => $filter,
                                            ],
                                        ],
                                    ],
                                    'should' => [],
                                    'minimum_should_match' => 1,
                                ],
                            ],
                        ],
                    ];
                    $values = explode(',', $values);
                    foreach ($values as $value) {
                        $params['body']['aggs']['aggs_all_filters']['filter']['bool']['filter'][$i]['nested']['query']['bool']['should'][] = [
                            'term' => [
                                'filters.value' => $value,
                            ],
                        ];
                    }

                    //                    $params['body']['post_filter']['bool']['filter'][$i] = [
                    //                      'nested' => [
                    //                          'path' => 'filters',
                    //                          'query' => [
                    //                              'bool' => [
                    //                                  'must' => [
                    //                                      [
                    //                                          'term' => [
                    //                                              'filters.name' => $filter
                    //                                          ]
                    //                                      ]
                    //                                  ],
                    //                                  'should' => [],
                    //                                  'minimum_should_match' => 1
                    //                              ]
                    //                          ]
                    //                      ]
                    //                    ];
                    //                    foreach($values as $value)
                    //                    {
                    //
                    //                        $params['body']['post_filter']['bool']['filter'][$i]['nested']['query']['bool']['should'] = [
                    //                            'term' => [
                    //                                'filters.value' => $value
                    //                            ]
                    //                        ];
                    //                    }
                    $i++;

                }
            }

        }

        $response = $this->getElasticSearchClient()->search($params);

        $documents = $response['hits']['hits'];
        dump($documents);
        $resultFacets = $this->buildFacets($response['aggregations']);

        $resultDocuments = [];
        foreach ($documents as $document) {

            $resultDocuments[] = [
                'title' => $document['_source']['title'],
                'description' => $document['_source']['description'],
                'price' => $document['_source']['price'],
                'filters' => $document['_source']['filters'] ?? [],
            ];

        }

        return [
            'documents' => $resultDocuments,
            'facets' => $resultFacets ?? [],
        ];
    }

    public function buildFacets($aggregations)
    {

        $facets = ['all' => [], 'debug' => $aggregations];
        if (isset($aggregations['aggs_all_filters'])) {
            foreach ($aggregations as $key => $aggs) {
                if ($key === 'aggs_all_filters') {
                    continue;
                }
                $key = str_replace('aggs_', '', $key);
                $facets[$key]['name'] = $key;
                $facets[$key]['values'] = $aggs['facets']['aggs_special']['names']['buckets'];

            }
            array_push($facets['all'], $aggregations['aggs_all_filters']['facets']['names']['buckets']);
        }

        return $facets;
    }
}

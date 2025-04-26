<?php

namespace App\Console\Commands;

use App\Models\Category;
use Elastic\Elasticsearch\Client;
use Illuminate\Console\Command;
use Elastic\Elasticsearch\ClientBuilder;

class CreateElasticsearchProductIndex extends Command
{

    protected $signature = 'elasticsearch:create-products-index';
    protected $description = 'Create the category_lang index in Elasticsearch';
    public function __construct(protected Client $client)
    {
        parent::__construct(); // Важно вызвать конструктор родительского класса
    }
    public function handle()
    {
        try {
        $client = ClientBuilder::create()
            ->setHosts([env('ELASTICSEARCH_HOST', 'http://elasticsearch:9200')])
            ->build();
        if ($client->indices()->exists(['index' => 'products_index'])->asBool()) {
            $client->indices()->delete(['index' => 'products_index']);
            $this->info("Index products deleted.");
        }
            $params = [
                'index' => 'products_index',
                'body'  => [
                    'settings' => [
                        'analysis' => [
                            'filter' => [
                                'autocomplete_filter' => [
                                    'type'     => 'edge_ngram',
                                    'min_gram' => 1,
                                    'max_gram' => 20,
                                ],
                            ],
                            'analyzer' => [
                                'autocomplete' => [
                                    'type'      => 'custom',
                                    'tokenizer' => 'standard',
                                    'filter'    => ['lowercase', 'autocomplete_filter'],
                                ],
                            ],
                        ],
                    ],
                    'mappings' => [
                        'properties' => [
                            'category_id' => [
                                'type' => 'integer',
                            ],
                            'category_title' => [
                                'type' => 'object',
                                'properties' => [
                                    'uk' => ['type' => 'text', 'analyzer' => 'autocomplete', 'search_analyzer' => 'standard'],
                                    'en' => ['type' => 'text', 'analyzer' => 'autocomplete', 'search_analyzer' => 'standard'],
                                ],
                            ],
                            'product_id' => ['type' => 'integer'],
                            'product_name' => [
                                'type' => 'object',
                                'properties' => [
                                    'uk' => ['type' => 'text', 'analyzer' => 'autocomplete', 'search_analyzer' => 'standard'],
                                    'en' => ['type' => 'text', 'analyzer' => 'autocomplete', 'search_analyzer' => 'standard'],
                                ],
                            ],
                            'brand_id' => ['type' => 'integer'],
                            'brand_name' => [
                                'type' => 'text',
                                'analyzer' => 'autocomplete',
                                'search_analyzer' => 'standard',
                                'fields' => [
                                    'keyword' => [
                                        'type' => 'keyword'
                                    ]
                                ]
                            ],
                        ],
                    ],
                ],
            ];
        $client->indices()->create($params);
        $this->info('Index category_lang created successfully.');
        $categories = Category::with(['products.brand.translation', 'translate', 'products'])->get();
            foreach ($categories as $category) {
                $categoryTitles = [];
                foreach ($category->translation as $catLang) {
                    $categoryTitles[$catLang->locale] = $catLang->title;
                }

                foreach ($category->products as $product) {
                    $productNames = [];
                    $brandNames = [];

                    foreach ($product->translation as $prodLang) {
                        $productNames[$prodLang->locale] = $prodLang->name;
                    }

                    $client->index([
                        'index' => 'products_index',
                        'body' => [
                            'category_id' => $category->id,
                            'category_title' => $categoryTitles,
                            'product_id' => $product->id,
                            'product_name' => $productNames,
                            'brand_id' => $product->brand_id,
                            'brand_name' => $product->brand?->name,
                        ]
                    ]);
                }
            }
            $this->info("Indexed {$categories->count()} records.");
        } catch (\Exception $e) {
            $this->error('Failed to create index: '.$e->getMessage());
        }
    }
}

<?php

namespace App\Console\Commands;

use App\Models\CategoryLang;
use Illuminate\Console\Command;
use Elastic\Elasticsearch\ClientBuilder;

class CreateElasticsearchIndex extends Command
{
    protected $signature = 'elasticsearch:create-category-index';
    protected $description = 'Create the category_lang index in Elasticsearch';

    public function handle()
    {

        $client = ClientBuilder::create()
            ->setHosts([env('ELASTICSEARCH_HOST', 'http://elasticsearch:9200')])
            ->build();
        if ($client->indices()->exists(['index' => 'category_lang'])->asBool()) {
            $client->indices()->delete(['index' => 'category_lang']);
            $this->info("Index category_lang deleted.");
        }
        $params = [
            'index' => 'category_lang',
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
                        'title' => [
                            'type'     => 'text',
                            'analyzer' => 'autocomplete',
                            'search_analyzer' => 'standard',
                        ],
                        'description' => ['type' => 'text'],
                        'locale' => ['type' => 'keyword'],
                    ],
                ],
            ],
        ];

        try {
            $client->indices()->create($params);
            $this->info('Index category_lang created successfully.');

            // Индексация данных из БД
            $categories = CategoryLang::all();
            foreach ($categories as $category) {
                $client->index([
                    'index' => 'category_lang',
                    'id'    => $category->id,
                    'body'  => [
                        'category_id' => $category->category_id,
                        'title'       => $category->title,
                        'description' => $category->description,
                        'locale' => $category->locale,
                    ]
                ]);
            }
            $this->info("Indexed {$categories->count()} records.");
        } catch (\Exception $e) {
            $this->error('Failed to create index: '.$e->getMessage());
        }
    }
}

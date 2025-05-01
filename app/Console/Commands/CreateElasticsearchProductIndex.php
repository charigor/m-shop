<?php

namespace App\Console\Commands;

use App\Models\Product;
use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;
use Illuminate\Console\Command;

class CreateElasticsearchProductIndex extends Command
{
    protected $signature = 'elasticsearch:create-products-index';

    protected $description = 'Create the products index in Elasticsearch';

    public function __construct(protected Client $client)
    {
        parent::__construct();
    }

    public function handle()
    {
        try {
            $client = ClientBuilder::create()
                ->setHosts([env('ELASTICSEARCH_HOST', 'http://elasticsearch:9200')])
                ->build();

            if ($client->indices()->exists(['index' => 'products_index'])->asBool()) {
                $client->indices()->delete(['index' => 'products_index']);
                $this->info('Index products deleted.');
            }

            // Define the index mapping
            $params = [
                'index' => 'products_index',
                'body' => [
                    'settings' => [
                        'analysis' => [
                            'filter' => [
                                'autocomplete_filter' => [
                                    'type' => 'edge_ngram',
                                    'min_gram' => 1,
                                    'max_gram' => 20,
                                ],
                            ],
                            'analyzer' => [
                                'autocomplete' => [
                                    'type' => 'custom',
                                    'tokenizer' => 'standard',
                                    'filter' => ['lowercase', 'autocomplete_filter'],
                                ],
                            ],
                        ],
                    ],
                    'mappings' => [
                        'properties' => [
                            'category_ids' => [
                                'type' => 'integer',
                            ],
                            'category_titles' => [
                                'type' => 'object',
                                'properties' => [
                                    'uk' => ['type' => 'text', 'analyzer' => 'autocomplete', 'search_analyzer' => 'standard'],
                                    'en' => ['type' => 'text', 'analyzer' => 'autocomplete', 'search_analyzer' => 'standard'],
                                ],
                            ],
                            'product_id' => ['type' => 'integer'],
                            'brand_id' => ['type' => 'integer'],
                            'price' => ['type' => 'float'],
                            'brand_name' => [
                                'type' => 'text',
                                'analyzer' => 'autocomplete',
                                'search_analyzer' => 'standard',
                                'fields' => [
                                    'keyword' => [
                                        'type' => 'keyword',
                                    ],
                                ],
                            ],
                            'product_attributes' => [
                                'type' => 'nested',
                                'properties' => [
                                    'attribute_name' => [
                                        'properties' => [
                                            'uk' => ['type' => 'keyword'],
                                            'en' => ['type' => 'keyword'],
                                        ],
                                    ],
                                    'attribute_value' => [
                                        'properties' => [
                                            'uk' => ['type' => 'keyword'],
                                            'en' => ['type' => 'keyword'],
                                        ],
                                    ],
                                ],
                            ],
                            'product_features' => [
                                'type' => 'nested',
                                'properties' => [
                                    'feature_name' => [
                                        'properties' => [
                                            'uk' => ['type' => 'keyword'],
                                            'en' => ['type' => 'keyword'],
                                        ],
                                    ],
                                    'feature_value' => [
                                        'properties' => [
                                            'uk' => ['type' => 'keyword'],
                                            'en' => ['type' => 'keyword'],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ];

            $client->indices()->create($params);

            // Fetch products with all needed relations
            $products = Product::with([
                'categories.translation',
                'brand.translation',
                'translation',
                'attributes.attributes.group.translation',
                'attributes.attributes.translation',
                'features.feature.translation',
                'features.translate'
            ])->get();

            $this->info("Starting to index {$products->count()} products...");
            $bar = $this->output->createProgressBar($products->count());

            foreach ($products as $product) {
                // Get categories info
                $categoryIds = [];
                $categoryTitles = [
                    'uk' => [],
                    'en' => []
                ];
                foreach ($product->categories as $category) {
                    $categoryIds[] = $category->id;

                    foreach ($category->translation as $catLang) {
                        $locale = $catLang->locale;
                        if (isset($categoryTitles[$locale])) {
                            $categoryTitles[$locale][] = $catLang->title;
                        }
                    }
                }

                // Process attributes
                $attributes = [];
                foreach ($product->attributes as $attributeProduct) {
                    foreach ($attributeProduct->attributes as $attribute) {
                        $attributes[] = [
                            'attribute_name' => [
                                'uk' => $attribute->group->translation->where('locale', 'uk')->first()?->name,
                                'en' => $attribute->group->translation->where('locale', 'en')->first()?->name,
                            ],
                            'attribute_value' => [
                                'uk' => $attribute->translation->where('locale', 'uk')->first()?->name,
                                'en' => $attribute->translation->where('locale', 'en')->first()?->name,
                            ],
                        ];
                    }
                }

                // Process features
                $features = [];
                foreach ($product->features as $featureValue) {
                    $feature = $featureValue->feature;

                    $featureNameUk = $feature->translation->where('locale', 'uk')->first()?->name;
                    $featureNameEn = $feature->translation->where('locale', 'en')->first()?->name;

                    $featureValueUk = $featureValue->translation->where('locale', 'uk')->first()?->value;
                    $featureValueEn = $featureValue->translation->where('locale', 'en')->first()?->value;

                    $features[] = [
                        'feature_name' => [
                            'uk' => $featureNameUk,
                            'en' => $featureNameEn,
                        ],
                        'feature_value' => [
                            'uk' => $featureValueUk,
                            'en' => $featureValueEn,
                        ],
                    ];
                }

                // Index the product
                $client->index([
                    'index' => 'products_index',
                    'id' => 'product_' . $product->id,
                    'body' => [
                        'category_ids' => $categoryIds,
                        'category_title' => $categoryTitles,
                        'product_id' => $product->id,
                        'brand_id' => $product->brand_id,
                        'brand_name' => $product->brand?->name,
                        'price' => $product->price,
                        'product_attributes' => $attributes,
                        'product_features' => $features,
                    ],
                ]);

                $bar->advance();
            }

            $bar->finish();
            $this->info("\nIndexed {$products->count()} products successfully.");

        } catch (\Exception $e) {
            $this->error('Failed to create index: ' . $e->getMessage());
            $this->error($e->getTraceAsString());
        }
    }
}

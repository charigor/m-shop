<?php

namespace App\Services\Filter\Elastic\Index;

use App\Models\Product;
use Elastic\Elasticsearch\Client;
use Illuminate\Support\Facades\Log;

class ProductIndex
{
    protected Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Index a single product in Elasticsearch
     *
     * @return bool Success status
     */
    public function indexProduct(Product $product): bool
    {
        try {
            // Make sure all necessary relations are loaded
            if (! $product->relationLoaded('categories') ||
                ! $product->relationLoaded('brand') ||
                ! $product->relationLoaded('attributes') ||
                ! $product->relationLoaded('features')) {
                $product->load([
                    'categories',
                    'brand.translation',
                    'attributes.attributes.group.translation',
                    'attributes.attributes.translation',
                    'features.feature.translation',
                    'features.translate',
                ]);
            }
            // Extract category ids
            $categoryIds = [];
            foreach ($product->categories as $category) {
                $categoryIds[] = $category->id;
            }

            // Process product attributes
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

            // Process product features
            $features = [];
            foreach ($product->features as $featureValue) {
                $feature = $featureValue->feature;

                $featureNameUk = $feature->translation->where('locale', 'uk')->first()?->name;
                $featureNameEn = $feature->translation->where('locale', 'en')->first()?->name;

                $featureValueUk = $featureValue->translation->where('locale', 'uk')->first()?->value;
                $featureValueEn = $featureValue->translation->where('locale', 'en')->first()?->value;

                $features[] = [
                    'feature_guard_name' => $feature->guard_name,
                    'feature_value_id' => $featureValue->id,
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

            // Index the product (create or update document)
            $this->client->index([
                'index' => 'products_index',
                'id' => 'product_'.$product->id,
                'body' => [
                    'category_ids' => $categoryIds,
                    'product_id' => $product->id,
                    'brand_id' => $product->brand_id,
                    'brand_name' => $product->brand?->name,
                    'price' => $product->price,
                    'product_attributes' => $attributes,
                    'product_features' => $features,
                ],
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error("Error indexing product ID: {$product->id} in Elasticsearch", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return false;
        }
    }

    /**
     * Delete a product from Elasticsearch index
     *
     * @return bool Success status
     */
    public function deleteProduct(int $productId): bool
    {
        try {
            $this->client->delete([
                'index' => 'products_index',
                'id' => 'product_'.$productId,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error("Error deleting product ID: {$productId} from Elasticsearch", [
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Create or recreate the products index
     *
     * @param  bool  $recreateIndex  Whether to delete the existing index if it exists
     * @return bool Success status
     */
    public function createIndex(bool $recreateIndex = false): bool
    {
        try {
            // Delete existing index if needed
            if ($recreateIndex && $this->client->indices()->exists(['index' => 'products_index'])->asBool()) {
                $this->client->indices()->delete(['index' => 'products_index']);
                Log::info('Index products_index deleted.');
            }

            // Define index settings and mappings
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
                                    'feature_guard_name' => ['type' => 'keyword'],
                                    'feature_value_id' => ['type' => 'integer'],

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

            $this->client->indices()->create($params);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to create Elasticsearch index: '.$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return false;
        }
    }

    /**
     * Index all products to Elasticsearch
     *
     * @return array Indexing statistics [total, indexed, errors]
     */
    public function indexAllProducts(): array
    {
        $stats = [
            'total' => 0,
            'indexed' => 0,
            'errors' => 0,
        ];

        // Get all products with necessary relations
        $products = Product::with([
            'categories',
            'brand.translation',
            'attributes.attributes.group.translation',
            'attributes.attributes.translation',
            'features.feature.translation',
            'features.translate',
        ])->get();

        $stats['total'] = $products->count();

        foreach ($products as $product) {
            if ($this->indexProduct($product)) {
                $stats['indexed']++;
            } else {
                $stats['errors']++;
            }
        }

        return $stats;
    }
}

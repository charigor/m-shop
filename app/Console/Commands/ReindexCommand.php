<?php

namespace App\Console\Commands;

use App\Models\ProductLang;
use Illuminate\Console\Command;

class ReindexCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'search:reindex';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Indexes all articles to Elasticsearch';

    //    public \Elastic\Elasticsearch\Client $elasticsearch;

    //    public function __construct(\Elastic\Elasticsearch\Client $elasticsearch)
    //    {
    //        parent::__construct();
    //
    //        $this->elasticsearch = $elasticsearch;
    //    }

    public function handle()
    {
        //        $this->info('Indexing all brands. This might take a while...');
        //
        //        foreach (ProductLang::cursor() as $product)
        //        {
        //            $this->elasticsearch->index([
        //                'index' => $product->getSearchIndex(),
        //                'type' => $product->getSearchType(),
        //                'id' => $product->getKey(),
        //                'body' => $product->toSearchArray(),
        //            ]);
        //
        //            // PHPUnit-style feedback
        //            $this->output->write('.');
        //        }
        //
        //        $this->info("\nDone!");
        // $data = {
        //	"mappings": {
        //		"dynamic": true,
        //		"properties": {
        //			"title": {
        //				"type": "text",
        //				"copy_to": "all_filters"
        //			},
        //			"description": {
        //				"type": "text",
        //				"copy_to": "all_filters"
        //			},
        //			"reference": {
        //				"type": "keyword",
        //				"copy_to": "all_filters"
        //			},
        //			"price": {
        //				"type": "float"
        //			},
        //			"active": {
        //				"type": "integer"
        //				"null_value": 0
        //			},
        //			"quantity": {
        //				"type": "integer"
        //			},
        //			"labels": {
        //				"type": "nested",
        //				"properties": {
        //					"label": {
        //						"type": "keyword",
        //						"copy_to": "all_filters"
        //					},
        //					"color": {
        //						"type": "keyword"
        //					}
        //				}
        //			},
        //			"images": {
        //				"type": "nested",
        //				"properties": {
        //					"is_main": {
        //						"type": "boolean",
        //						"null_value": false
        //					},
        //					"thumbnail": {
        //						"type": "text"
        //					},
        //					"medium": {
        //						"type": "text"
        //					},
        //					"original": {
        //						"type": "text"
        //					}
        //				}
        //			},
        //			"filters": {
        //				"type": "nested",
        //				"properties": {
        //					"name": {
        //						"type": "keyword",
        //						"copy_to": "all_filters"
        //					},
        //					"value": {
        //						"type": "keyword",
        //						"copy_to": "all_filters"
        //					},
        //					"pretty_name": {
        //						"type": "keyword",
        //						"copy_to": "all_filters"
        //					}
        //				}
        //			}
        //		}
        //	}
        // }'
    }
}

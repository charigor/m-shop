<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;
use Meilisearch\Client;

class UpdateProductSearchAttributesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'search:product.update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command search';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $client = new Client('meilisearch:7700');
        $client->index('products')->updateSortableAttributes(
            Product::SORTABLE
        );
        $client->index('products')->updateRankingRules([
            'quantity:desc',
            'words',
            'typo',
            'proximity',
            'attribute',
            'sort',
            'exactness',
        ]
        );
        $client->index('products')->updateFilterableAttributes(Product::FILTERABLE);

    }
}

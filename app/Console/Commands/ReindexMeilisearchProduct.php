<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;
use Meilisearch\Client;

class ReindexMeilisearchProduct extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:reindex-meilisearch-product {id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    //    protected int $productId;
    //    public function __construct($id)
    //    {
    //        parent::__construct();
    //
    //        $this->productId = $id;
    //    }
    public function handle()
    {
        $client = new Client('meilisearch:7700');
        $client->index('products')->updateDocuments(['id' => $this->argument('id')]);
        $client->index('products')->updateSortableAttributes(
            Product::SORTABLE
        );
        $client->index('products')->updateFilterableAttributes(Product::FILTERABLE);
    }
}

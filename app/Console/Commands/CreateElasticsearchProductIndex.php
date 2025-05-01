<?php

namespace App\Console\Commands;

use App\Services\Filter\Elastic\Index\ProductIndex;
use Illuminate\Console\Command;

class CreateElasticsearchProductIndex extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'elasticsearch:create-products-index {--recreate : Recreate index if exists}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create the products index in Elasticsearch';

    /**
     * The product index service.
     */
    protected ProductIndex $productIndex;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(ProductIndex $productIndex)
    {
        parent::__construct();
        $this->productIndex = $productIndex;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            // Get the recreate option
            $recreate = $this->option('recreate') || true; // Default to true since your original code always recreates

            // Create/recreate the index using our service
            $this->info('Creating Elasticsearch products index...');
            if (! $this->productIndex->createIndex($recreate)) {
                $this->error('Failed to create Elasticsearch index.');

                return 1;
            }

            $this->info('Index created successfully. Indexing products...');

            // Index all products with a progress bar
            $stats = $this->indexProductsWithProgressBar();

            $this->info("\nIndexed {$stats['indexed']} of {$stats['total']} products successfully.");

            if ($stats['errors'] > 0) {
                $this->warn("{$stats['errors']} errors occurred during indexing. Check the logs for details.");
            }

            return 0;
        } catch (\Exception $e) {
            $this->error('Command failed: '.$e->getMessage());
            $this->error($e->getTraceAsString());

            return 1;
        }
    }

    /**
     * Index all products with a progress bar
     */
    protected function indexProductsWithProgressBar(): array
    {
        // Initialize stats
        $stats = [
            'total' => 0,
            'indexed' => 0,
            'errors' => 0,
        ];

        // Get all products with necessary relations
        $products = \App\Models\Product::with([
            'categories.translation',
            'brand.translation',
            'translation',
            'attributes.attributes.group.translation',
            'attributes.attributes.translation',
            'features.feature.translation',
            'features.translate',
        ])->get();

        $stats['total'] = $products->count();

        // Create a progress bar
        $this->info("Starting to index {$products->count()} products...");
        $bar = $this->output->createProgressBar($products->count());

        // Index each product
        foreach ($products as $product) {
            if ($this->productIndex->indexProduct($product)) {
                $stats['indexed']++;
            } else {
                $stats['errors']++;
            }

            $bar->advance();
        }

        $bar->finish();

        return $stats;
    }
}

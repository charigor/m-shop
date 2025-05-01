<?php

namespace App\Jobs;

use App\Services\Filter\Elastic\Index\ProductIndex;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class DeleteElasticsearchProduct implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Number of attempts to run the job.
     */
    public int $tries = 3;

    /**
     * Product ID to be deleted from index
     */
    protected int $productId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(int $productId)
    {
        $this->productId = $productId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(ProductIndex $indexService)
    {
        try {
            if (! $indexService->deleteProduct($this->productId)) {
                throw new \Exception('Failed to delete product from Elasticsearch');
            }

            Log::info("Product deleted from Elasticsearch, ID: {$this->productId}");
        } catch (\Exception $e) {
            Log::error("Error deleting product from Elasticsearch, ID: {$this->productId}", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e; // Rethrow exception for retry attempts
        }
    }
}

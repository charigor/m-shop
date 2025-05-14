<?php

namespace App\Jobs;

use App\Models\Product;
use App\Services\Filter\Elastic\Index\ProductIndex;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class UpdateElasticsearchProduct implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    protected int $productId;

    /**
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
            $product = Product::findOrFail($this->productId);
            if (! $indexService->indexProduct($product)) {
                throw new \Exception('Failed to delete product from Elasticsearch');
            }

            Log::info("Product deleted from Elasticsearch, ID: {$this->productId}");

            Log::info("Elasticsearch document updated for product ID: {$this->productId}");
        } catch (\Exception $e) {
            Log::error("Error updating Elasticsearch document for product ID: {$this->productId}", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e; // Перебросим исключение для повторной попытки
        }
    }
}

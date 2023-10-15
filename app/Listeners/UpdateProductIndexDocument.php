<?php

namespace App\Listeners;

use App\Events\ProductUpdateIndex;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Artisan;
use Meilisearch\Client;

class UpdateProductIndexDocument implements ShouldQueue
{
    public string $queue = 'product_index';

    /**
     * The time (seconds) before the job should be processed.
     *
     * @var int
     */
    public int $delay = 60;
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(ProductUpdateIndex $event): void
    {
//        Artisan::call('app:reindex-meilisearch-product', [
//            'id' => $event->product->id
//        ]);
        info('event');
        info($event->product->id);
        $client = new Client('meilisearch:7700');
        $client->index('products')->updateDocuments(['id' => $event->product->id]);
    }
//    public function shouldQueue(ProductUpdateIndex $event): void
//    {
//
//        return true;
//    }
}

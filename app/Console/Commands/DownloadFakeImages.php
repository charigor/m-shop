<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class DownloadFakeImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:download-fake-images';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {

            $dir = storage_path('app/public/fake-images');
            if (!file_exists($dir)) {
                mkdir($dir, 0777, true);
            }

        for ($i = 1; $i <= 100; $i++) {
            $imageUrl = "https://picsum.photos/seed/$i/600/400";
            $imageContents = file_get_contents($imageUrl);
            file_put_contents("$dir/image_$i.jpg", $imageContents);
            $this->info("Saved image_$i.jpg");
        }

            $this->info('All images downloaded!');
    }
}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class RemoveTempImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tmp_image:remove';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove old temp images from temp folder';

    public function handle()
    {
        $this->info('The process of deleting the old image has started. This might take a while...');
        $files = Storage::disk('public')->allFiles('tmp');
        foreach ($files as $filepath) {
            $date = getStringBetween($filepath, 'tmp/uploads/', '_');

            if ($date < now()->subDays(1)->timestamp) {
                Storage::delete('public/'.$filepath);
            }
        }
        $this->info('The process of deleting the old temporary image is complete.');

    }
}

<?php namespace Modules\Media\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;

class CleanThumbnails implements ShouldQueue
{
    public function handle()
    {
        $imagy = app('imagy');

        app('log')->info('Cleaning thumbnails');

        $imagy->cleanFiles();
    }
}
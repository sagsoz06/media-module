<?php

namespace Modules\Media\Console;

use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Modules\Media\Jobs\CleanThumbnails;
use Modules\Media\Jobs\RebuildThumbnails;
use Modules\Media\Repositories\FileRepository;

class RefreshThumbnailCommand extends Command
{
    use DispatchesJobs;
    protected $signature = 'asgard:media:refresh {clean?}';
    protected $description = 'Create and or refresh the thumbnails';
    /**
     * @var FileRepository
     */
    private $file;

    public function __construct(FileRepository $file)
    {
        parent::__construct();
        $this->file = $file;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        $this->line('Cleaning thumbnails...');

        $this->dispatch(new CleanThumbnails());

        $this->line('All thumbnails cleaned');

        $this->line('Preparing to regenerate all thumbnails...');

        $clean = $this->argument('clean');

        if($clean) {
            $this->dispatch(new RebuildThumbnails($this->file->all()->pluck('path'), true));
        } else {
            $this->dispatch(new RebuildThumbnails($this->file->all()->pluck('path')));
        }

        $this->info('All thumbnails refreshed');
    }
}

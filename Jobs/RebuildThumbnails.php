<?php

namespace Modules\Media\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Modules\Media\Repositories\FileRepository;

class RebuildThumbnails implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    /**
     * @var Collection
     */
    private $paths;
    /**
     * @var FileRepository
     */
    private $file;
    /**
     * @var bool
     */
    private $clean;

    public function __construct(Collection $paths, $clean=false)
    {
        $this->paths = $paths;
        $this->file = app(FileRepository::class);
        $this->clean = $clean;
    }

    public function handle()
    {
        $imagy = app('imagy');

        if($this->clean) {
            $mediaPath   = public_path(config('asgard.media.config.files-path'));
            $allFiles    = collect(preg_grep('/^([^.])/', scandir($mediaPath)));
            $getFiles    = $this->file->all();
            $exceptFiles = $allFiles->map(function($item, $key){
                return [
                    'id'       => $key,
                    'filename' => $item
                ];
            })->values()->whereIn('filename', $getFiles->pluck('filename', 'id'));
            $allFiles->each(function($filename) use ($exceptFiles, $mediaPath){
                if(!$exceptFiles->where('filename', $filename)->count()>0) {
                    \File::delete($mediaPath.$filename);
                }
            });
        }

        foreach ($this->paths as $path) {
            try {
                $imagy->createAll($path);
                app('log')->info('Generating thumbnails for path: ' . $path);
            } catch (\Exception $e) {
                app('log')->warning('File not found: ' . $path);
            }
        }
    }
}

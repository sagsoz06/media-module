<?php

namespace Modules\Media\Http\Controllers\Admin;

use Illuminate\Contracts\Config\Repository;
use Modules\Core\Http\Controllers\Admin\AdminBaseController;
use Modules\Media\Entities\File;
use Modules\Media\Http\Requests\UpdateMediaRequest;
use Modules\Media\Image\Imagy;
use Modules\Media\Image\ThumbnailManager;
use Modules\Media\Repositories\FileRepository;

class MediaController extends AdminBaseController
{
    /**
     * @var FileRepository
     */
    private $file;
    /**
     * @var Repository
     */
    private $config;
    /**
     * @var Imagy
     */
    private $imagy;
    /**
     * @var ThumbnailManager
     */
    private $thumbnailsManager;

    public function __construct(FileRepository $file, Repository $config, Imagy $imagy, ThumbnailManager $thumbnailsManager)
    {
        parent::__construct();
        $this->file = $file;
        $this->config = $config;
        $this->imagy = $imagy;
        $this->thumbnailsManager = $thumbnailsManager;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $files = $this->file->all();

        if(request()->ajax()) {
            return \Datatables::of($files)
                ->addColumn('thumbnail', function($file){
                    if ($file->isImage()):
                        return \Html::image(\Imagy::getThumbnail($file->path, 'smallThumb'));
                    else:
                        return '<i class="fa '.\FileHelper::getFaIcon($file->media_type).'" style="font-size: 20px;"></i>';
                    endif;
                })
                ->addColumn('action', function ($file) {
                    $action_buttons =   \Html::decode(link_to(
                        route('admin.media.media.edit',
                            [$file->id]),
                        '<i class="fa fa-pencil"></i>',
                        ['class'=>'btn btn-default btn-flat']
                    ));
                    $action_buttons .=  \Html::decode(\Form::button(
                        '<i class="fa fa-trash"></i>',
                        ["data-toggle" => "modal",
                            "data-action-target" => route("admin.media.media.destroy", [$file->id]),
                            "data-target" => "#modal-delete-confirmation",
                            "data-method" => "delete",
                            "data-token" => csrf_token(),
                            "class"=>"btn btn-danger btn-flat"
                        ]
                    ));
                    return $action_buttons;
                })
                ->escapeColumns([])
                ->make(true);
        }

        $config = $this->config->get('asgard.media.config');

        return view('media::admin.index', compact('files', 'config'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return view('media.create');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  File     $file
     * @return Response
     */
    public function edit(File $file)
    {
        $thumbnails = $this->thumbnailsManager->all();

        return view('media::admin.edit', compact('file', 'thumbnails'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  File               $file
     * @param  UpdateMediaRequest $request
     * @return Response
     */
    public function update(File $file, UpdateMediaRequest $request)
    {
        $this->file->update($file, $request->all());

        return redirect()->route('admin.media.media.index')
            ->withSuccess(trans('media::messages.file updated'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  File     $file
     * @internal param int $id
     * @return Response
     */
    public function destroy(File $file)
    {
        $this->imagy->deleteAllFor($file);
        $this->file->destroy($file);

        return redirect()->route('admin.media.media.index')
            ->withSuccess(trans('media::messages.file deleted'));
    }
}

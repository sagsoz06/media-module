<?php

namespace Modules\Media\Http\Controllers\Admin;

use Modules\Core\Http\Controllers\Admin\AdminBaseController;
use Modules\Media\Image\ThumbnailManager;
use Modules\Media\Repositories\FileRepository;

class MediaGridController extends AdminBaseController
{
    /**
     * @var FileRepository
     */
    private $file;
    /**
     * @var ThumbnailManager
     */
    private $thumbnailsManager;

    public function __construct(FileRepository $file, ThumbnailManager $thumbnailsManager)
    {
        parent::__construct();

        $this->file = $file;
        $this->thumbnailsManager = $thumbnailsManager;
    }

    /**
     * A grid view for the upload button
     * @return \Illuminate\View\View
     */
    public function index()
    {
//        $files = $this->file->all();
//        $thumbnails = $this->thumbnailsManager->all();
//
//        return view('media::admin.grid.general', compact('files', 'thumbnails'));
        
        return $this->ckIndexAjax('general');
    }

    /**
     * A grid view of uploaded files used for the wysiwyg editor
     * @return \Illuminate\View\View
     */
    public function ckIndex()
    {
//        $files = $this->file->all();
//        $thumbnails = $this->thumbnailsManager->all();
//
//        return view('media::admin.grid.ckeditor', compact('files', 'thumbnails'));

        return $this->ckIndexAjax('ckeditor');
    }

    private function ckIndexAjax($view)
    {
        $files = $this->file->query();
        $thumbnails = $this->thumbnailsManager->all();

        if(request()->ajax()) {
            return \Datatables::of($files)
                ->addColumn('thumbnail', function($file){
                    if ($file->isImage()):
                        return \Html::image(\Imagy::getThumbnail($file->path, 'smallThumb'));
                    else:
                        return '<i class="fa '.\FileHelper::getFaIcon($file->media_type).'" style="font-size: 20px;"></i>';
                    endif;
                })
                ->addColumn('action', function ($file) use ($thumbnails) {
                    $insertButton  = '<div class="btn-group">';
                    $insertButton .= '<button type="button" class="btn btn-primary btn-flat dropdown-toggle" data-toggle="dropdown" aria-expanded="false">';
                    $insertButton .= trans('media::media.insert') . '<span class="caret"></span>';
                    $insertButton .= '</button>';

                    $insertButton .= '<ul class="dropdown-menu" role="menu">';
                    foreach ($thumbnails as $thumbnail):
                        $insertButton .= '<li data-file-path="'.\Imagy::getThumbnail($file->path, $thumbnail->name()).'"';
                        $insertButton .= 'data-id="'.$file->id.'" data-media-type="'.$file->media_type.'"';
                        $insertButton .= 'data-mimetype="'.$file->mimetype.'" class="jsInsertImage">';
                        $insertButton .= '<a href="">'.$thumbnail->name().' ('.$thumbnail->size().')</a>';
                        $insertButton .= '</li>';
                    endforeach;

                    $insertButton .= '<li class="divider"></li>';
                    $insertButton .= '<li data-file-path="'.$file->path.'" data-id="'.$file->id.'"';
                    $insertButton .= 'data-media-type="'.$file->media_type.'" data-mimetype="'.$file->mimetype.'" class="jsInsertImage">';
                    $insertButton .= '<a href="">Orjinal</a>';
                    $insertButton .= '</li>';

                    $insertButton .= '</ul>';
                    $insertButton .= '</div>';

                    return $insertButton;

                })
                ->addColumn('insertAction', function ($file) {
                    $insertButton  = '<a href="" class="btn btn-primary jsInsertImage btn-flat" data-id="'.$file->id.'"';
                    $insertButton .= 'data-file-path="'.\Imagy::getThumbnail($file->path, 'mediumThumb').'"';
                    $insertButton .= 'data-media-type="'.$file->media_type.'" data-mimetype="'.$file->mimetype.'">';
                    $insertButton .= trans('media::media.insert');
                    $insertButton .= '</a>';
                    return $insertButton;
                })
                ->escapeColumns([])
                ->make(true);
        }

        return view('media::admin.grid.'.$view, compact('files'));
    }
}

<?php

namespace Modules\Media\Events;

use Modules\Core\Events\AbstractEntityHook;
use Modules\Core\Contracts\EntityIsChanging;
use Modules\Media\Entities\File;

final class FileIsUpdating extends AbstractEntityHook implements EntityIsChanging
{
    private $file;

    public function __construct(File $file, array $data)
    {
        $this->file = $file;
        parent::__construct($data);
    }
}

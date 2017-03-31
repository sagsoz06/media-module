<?php

return [
    'media.medias' => [
        'index'   => 'media::media.list resource',
        'create'  => 'media::media.create resource',
        'edit'    => 'media::media.edit resource',
        'destroy' => 'media::media.destroy resource',
    ],
    'api.media'    => [
        'store'  => 'media::media.api.store resource',
        'link'   => 'media::media.api.link resource',
        'unlink' => 'media::media.api.unlink resource',
        'all'    => 'media::media.api.all resource',
        'sort'   => 'media::media.api.sort resource',
    ]
];

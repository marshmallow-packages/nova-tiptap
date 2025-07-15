<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Image Pruning
    |--------------------------------------------------------------------------
    |
    | When enabled, this will automatically remove uploaded images from storage
    | when they are deleted from the TipTap content. Only images with
    | tt-mode="file" will be pruned (uploaded files, not external URLs).
    |
    */
    'prune_images' => false,

    /*
    |--------------------------------------------------------------------------
    | Image Storage Settings
    |--------------------------------------------------------------------------
    |
    | Default settings for image storage when not explicitly set on the field.
    |
    */
    'image_storage' => [
        'disk' => 'public',
        'path' => '',
    ],
];

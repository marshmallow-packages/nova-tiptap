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

    /*
    |--------------------------------------------------------------------------
    | File Storage Settings
    |--------------------------------------------------------------------------
    |
    | Default settings for file storage when not explicitly set on the field.
    |
    */
    'file_storage' => [
        'disk' => 'public',
        'path' => '',
    ],

    /*
    |--------------------------------------------------------------------------
    | Upload Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for file uploads
    |
    */
    'upload' => [
        /*
         | Maximum file size in bytes
         | Images: 5MB, Files: 10MB
         */
        'max_image_size' => 5242880, // 5MB
        'max_file_size' => 10485760, // 10MB

        /*
         | Allowed file extensions for uploads
         */
        'allowed_image_extensions' => ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'],
        'allowed_file_extensions' => [
            'pdf',
            'doc',
            'docx',
            'xls',
            'xlsx',
            'ppt',
            'pptx',
            'txt',
            'rtf',
            'csv',
            'zip',
            'rar',
            '7z',
            'jpg',
            'jpeg',
            'png',
            'gif',
            'webp',
            'svg',
            'mp3',
            'wav',
            'mp4',
            'avi',
            'mov',
            'wmv'
        ],

        /*
         | Allowed storage disks for uploads
         | For security, only allow specific disks
         */
        'allowed_disks' => ['public', 'local', 'linode'],

        /*
         | Enable strict MIME type checking
         | This validates that file content matches the extension
         */
        'strict_mime_checking' => true,
    ],
];

<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application. Just store away!
    |
    */

    'default' => env('FILESYSTEM_DISK', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Here you may configure as many filesystem "disks" as you wish, and you
    | may even configure multiple disks of the same driver. Defaults have
    | been set up for each driver as an example of the required values.
    |
    | Supported Drivers: "local", "ftp", "sftp", "s3"
    |
    */

    'disks'   => [

        'campus_clubs' => [
            'driver' => 'local',
            'root'   => storage_path('app/private/campus-clubs'),
            'throw'  => true,
        ],

        'local'        => [
            'driver' => 'local',
            'root'   => storage_path('app'),
            'throw'  => false,
        ],

        'public'       => [
            'driver'     => 'local',
            'root'       => storage_path('app/public'),
            'url'        => env('APP_URL').'/storage',
            'visibility' => 'public',
            'throw'      => false,
        ],

        'storage'      => [
            'driver'                  => 's3',
            'key'                     => env('WPORTAL_STORAGE_ACCESS_KEY'),
            'secret'                  => env('WPORTAL_STORAGE_SECRET_KEY'),
            'region'                  => env('WPORTAL_STORAGE_DEFAULT_REGION'),
            'bucket'                  => env('WPORTAL_STORAGE_BUCKET'),
            'url'                     => env('WPORTAL_STORAGE_AWS_URL'),
            'endpoint'                => env('WPORTAL_STORAGE_ENDPOINT'),
            'use_path_style_endpoint' => env('WPORTAL_STORAGE_USE_PATH_STYLE_ENDPOINT', false),
            'throw'                   => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Symbolic Links
    |--------------------------------------------------------------------------
    |
    | Here you may configure the symbolic links that will be created when the
    | `storage:link` Artisan command is executed. The array keys should be
    | the locations of the links and the values should be their targets.
    |
    */

    'links'   => [
        public_path('storage') => storage_path('app/public'),
    ],

];

<?php

return [

    /*
    |----------------------------------------------------------------------
    | Chunk Limit
    |----------------------------------------------------------------------
    |
    | To avoid failure during bulk update on a huge record, database query
    | is separated into smaller chunk.
    |
    */

    'chunk' => 100,

    /*
    |----------------------------------------------------------------------
    | Driver Configuration
    |----------------------------------------------------------------------
    |
    | Setup your driver configuration to let us match the driver name to
    | a Model and path to migration.
    |
    */

    'drivers' => [
        'user' => [
            'model' => 'App\User',
            'path'  => database_path('tenant/users'),
        ],
    ],
];

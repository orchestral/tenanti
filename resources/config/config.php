<?php

return [

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
            'path' => database_path('tenant/users'),
            'shared' => true,
        ],
    ],
];

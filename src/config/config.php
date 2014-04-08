<?php

return [

    'drivers' => [
        'user' => [
            'model'   => 'User',
            'version' => 1,
            'path'    => app_path().'/database/tenant/users/',
        ],
    ],
];

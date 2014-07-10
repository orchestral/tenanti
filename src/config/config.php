<?php

return [
    'migrations' => 'tenant_migrations',

    'drivers' => [
        'user' => [
            'model'   => 'User',
            'path'    => app_path().'/database/tenant/users',
        ],
    ],
];

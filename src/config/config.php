<?php

return [
    'migrations' => 'tenant_migrations',

    'drivers' => [
        'user' => [
            'model'     => 'User',
            'migration' => 'user_migrations',
            'path'      => app_path().'/database/tenant/users',
        ],
    ],
];

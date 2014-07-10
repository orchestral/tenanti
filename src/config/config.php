<?php

return array(

    'chunck'  => 1000,

    'drivers' => array(
        'user' => array(
            'model'     => 'User',
            'migration' => 'user_{id}_migrations',
            'path'      => app_path().'/database/tenant/users',
        ),
    ),
);

<?php

return [
    'admin' => [
        'name' => env(
            'ADMIN_NAME',
            'Admin SiRental'
        ),

        'email' => env(
            'ADMIN_EMAIL',
            'admin@sirental.com'
        ),

        'password' => env(
            'ADMIN_PASSWORD'
        ),
    ],
];

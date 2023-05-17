<?php

return [
    'namespaces' => [
        'models' => 'App\\Models\\',
        'controllers' => 'App\\Http\\Controllers\\',
    ],
    'auth' => [
        'guard' => 'api',
    ],
    'specs' => [
        'info' => [
            'title' => env('APP_NAME'),
            'description' => 'The PERSCOM API describes how to interact and manipulate your PERSCOM data available at https://perscom.io. The API provides a powerful interface to allow for third-party collaboration and integration with your PERSCOM Dashboard.',
            'terms_of_service' => 'https://perscom.io/privacy-policy',
            'contact' => [
                'name' => 'Deschutes Design Group LLC',
                'url' => 'https://www.deschutesdesigngroup.com',
                'email' => 'info@deschutesdesigngroup.com',
            ],
            'license' => [
                'name' => null,
                'url' => null,
            ],
            'version' => env('API_VERSION'),
        ],
        'servers' => [
            ['url' => 'https://api.perscom.io', 'description' => 'Production Environment'],
        ],
        'tags' => [],
    ],
    'transactions' => [
        'enabled' => false,
    ],
    'search' => [
        'case_sensitive' => true, // TODO: set to "false" by default in 3.0 release
        /*
         |--------------------------------------------------------------------------
         | Max Nested Depth
         |--------------------------------------------------------------------------
         |
         | This value is the maximum depth of nested filters.
         | You will most likely need this to be maximum at 1, but
         | you can increase this number, if necessary. Please
         | be aware that the depth generate dynamic rules and can slow
         | your application if someone sends a request with thousands of nested
         | filters.
         |
         */
        'max_nested_depth' => 1,
    ],

    'use_validated' => false,
];

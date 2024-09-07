<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | API Versions
    |--------------------------------------------------------------------------
    |
    | An array of available API versions. The API controllers will check if an
    | HTTP resource exists for the newest version first and fallback to an
    | earlier version until an HTTP resource is found for the matching endpoint.
    | List the version in order of oldest to newest.
    |
    */

    'version' => env('API_VERSION', 'v2'),
    'versions' => [
        'v1',
        'v2',
    ],

    /*
    |--------------------------------------------------------------------------
    | API URL
    |--------------------------------------------------------------------------
    |
    | The current API url.
    |
    */

    'url' => env('API_URL', 'http://api.lvh.me'),
];

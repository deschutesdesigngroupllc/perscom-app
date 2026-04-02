<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Date;

return [
    'name' => 'WCountries',

    'locale_key' => config('translatable.locale_key', 'locale'),

    'cache' => [
        'is_cached' => true,
        'big_time' => Date::now()->addDays(120),
        'small_time' => Date::now()->addDays(7),
        'prefix' => null,
    ],
];

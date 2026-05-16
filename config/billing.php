<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Stripe Price → Plan Mapping
    |--------------------------------------------------------------------------
    |
    | Maps Stripe Price IDs to internal plan metadata. Used to resolve a
    | tenant's plan name and renewal interval from its active subscription.
    |
    */

    'plans' => [
        env('STRIPE_PRODUCT_MONTH') => [
            'name' => 'perscom',
            'interval' => 'monthly',
        ],
        env('STRIPE_PRODUCT_YEAR') => [
            'name' => 'perscom',
            'interval' => 'yearly',
        ],
    ],
];

<?php

declare(strict_types=1);

use App\Models\User;

return [
    /*
    |--------------------------------------------------------------------------
    | Updatable Models
    |--------------------------------------------------------------------------
    |
    | This configuration defines which models can be updated via automations.
    | Use ['*'] for allowed_fields to permit all fillable fields.
    | Fields in denied_fields are always blocked, even if allowed_fields is ['*'].
    |
    */

    'updatable_models' => [
        'user' => [
            'model' => User::class,
            'label' => 'User',
            'allowed_fields' => ['*'],
            'denied_fields' => [
                'password',
                'remember_token',
                'email_verified_at',
            ],
        ],
    ],
];

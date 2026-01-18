<?php

declare(strict_types=1);

use App\Models\Enums\AutomationActionType;
use App\Models\Enums\AutomationTrigger;
use App\Models\Enums\ModelUpdateLookupType;
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

    /*
    |--------------------------------------------------------------------------
    | Automation Templates
    |--------------------------------------------------------------------------
    |
    | Pre-configured automation templates that can be selected when creating
    | a new automation. Each template provides default values for the form.
    |
    */

    'templates' => [
        'increment_promotion_points_on_service_record' => [
            'name' => 'Increment Promotion Points on Service Record',
            'description' => "Automatically adds promotion points to a user when a service record is created. The points value is taken from the service record's custom fields.",
            'category' => 'User Management',
            'prerequisites' => [
                'Create a "Promotion Points" field with key `promotion_points` and add it to the Service Record and User\'s form.',
            ],
            'data' => [
                'name' => 'Increment Promotion Points on Service Record',
                'description' => '<p>Adds promotion points to a user when a new service record is created for them.</p>',
                'trigger' => AutomationTrigger::SERVICE_RECORD_CREATED,
                'action_type' => AutomationActionType::MODEL_UPDATE,
                'model_update_target' => 'user',
                'model_update_lookup_type' => ModelUpdateLookupType::EXPRESSION,
                'model_update_lookup_expression' => 'model.user_id',
                'model_update_fields' => [
                    'promotion_points' => '{{ target.promotion_points | increment(model.promotion_points) }}',
                ],
                'priority' => 0,
                'enabled' => true,
            ],
        ],
    ],
];

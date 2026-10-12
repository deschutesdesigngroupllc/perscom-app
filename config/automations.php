<?php

declare(strict_types=1);

use App\Models\Enums\AutomationActionType;
use App\Models\Enums\AutomationRunLimit;
use App\Models\Enums\AutomationTrigger;
use App\Models\Enums\ModelUpdateLookupType;
use App\Models\Enums\NotificationChannel;
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
                'model_update_lookup_expression' => 'model["user_id"]',
                'model_update_fields' => [
                    'promotion_points' => '{{ target.promotion_points | increment(model.promotion_points) }}',
                ],
                'priority' => 0,
                'enabled' => true,
            ],
        ],

        'promote_after_time_in_grade_with_qualification' => [
            'name' => 'Promote After 30 Days Time in Grade',
            'description' => 'Runs during the daily user review and promotes a user to the next rank once they have spent 30 days in their current rank and hold a required qualification.',
            'category' => 'User Management',
            'prerequisites' => [
                'Replace the rank and qualification IDs in the condition with the IDs from your organization.',
                'Replace the rank ID in the field mapping with the ID of the rank to promote to.',
                'Updating the rank directly does not create a rank record for the promotion.',
            ],
            'data' => [
                'name' => 'Promote After 30 Days Time in Grade',
                'description' => '<p>Promotes a user to the next rank after 30 days in grade when they hold the required qualification.</p>',
                'trigger' => AutomationTrigger::USER_DAILY_REVIEW,
                'condition' => 'model["rank_id"] == 1 and model["time_in_grade_days"] >= 30 and in_array(1, model["qualification_ids"])',
                'action_type' => AutomationActionType::MODEL_UPDATE,
                'model_update_target' => 'user',
                'model_update_lookup_type' => ModelUpdateLookupType::EXPRESSION,
                'model_update_lookup_expression' => 'model["id"]',
                'model_update_fields' => [
                    'rank_id' => '2',
                ],
                'run_limit' => AutomationRunLimit::ONCE_PER_MODEL,
                'priority' => 0,
                'enabled' => true,
            ],
        ],

        'remind_before_credential_expires' => [
            'name' => 'Remind 14 Days Before a Credential Expires',
            'description' => 'Sends the user a message 14 days before one of their credentials expires.',
            'category' => 'Readiness',
            'prerequisites' => [
                'Set a validity period on the credentials you want to track.',
            ],
            'data' => [
                'name' => 'Remind 14 Days Before a Credential Expires',
                'description' => '<p>Reminds a user to renew a credential 14 days before it expires.</p>',
                'trigger' => AutomationTrigger::CREDENTIAL_EXPIRING,
                'condition' => 'model["days_until_expiry"] == 14',
                'action_type' => AutomationActionType::MESSAGE,
                'message_channels' => [
                    NotificationChannel::DATABASE,
                    NotificationChannel::MAIL,
                ],
                'message_content' => '<p>Your {{ model.credential_name }} credential expires in {{ model.days_until_expiry }} days. Please schedule your renewal.</p>',
                'message_recipients_expression' => 'model["user_id"]',
                'run_limit' => AutomationRunLimit::ONCE_PER_DAY,
                'priority' => 0,
                'enabled' => true,
            ],
        ],

        'set_inactive_after_thirty_days_unseen' => [
            'name' => 'Set Inactive After 30 Days Unseen',
            'description' => "Runs during the daily user review and changes a user's status to Inactive when they have not been seen for 30 days.",
            'category' => 'User Management',
            'prerequisites' => [
                'Replace the status ID in the condition and field mapping with the ID of your Inactive status.',
            ],
            'data' => [
                'name' => 'Set Inactive After 30 Days Unseen',
                'description' => '<p>Marks a user as inactive after 30 days without signing in.</p>',
                'trigger' => AutomationTrigger::USER_DAILY_REVIEW,
                'condition' => 'model["days_since_last_seen"] != null and model["days_since_last_seen"] >= 30 and model["status_id"] != 1',
                'action_type' => AutomationActionType::MODEL_UPDATE,
                'model_update_target' => 'user',
                'model_update_lookup_type' => ModelUpdateLookupType::EXPRESSION,
                'model_update_lookup_expression' => 'model["id"]',
                'model_update_fields' => [
                    'status_id' => '1',
                ],
                'run_limit' => AutomationRunLimit::ALWAYS,
                'priority' => 0,
                'enabled' => true,
            ],
        ],
    ],
];

<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Padmission\DataLens\Mail\ReportEmail;

return [
    /*
    |==========================================================================
    | Core Settings
    |==========================================================================
    */

    'user_model' => User::class,

    'storage_disk' => env('FILESYSTEM_DISK', 'local'),

    /*
    |==========================================================================
    | Multi-Tenancy
    |==========================================================================
    |
    | Enable this to automatically scope all reports to tenants.
    */

    'tenant_aware' => false,

    'tenant_context' => [
        'key' => 'tenant_id',
    ],

    /*
    |==========================================================================
    | Model & Data Access Control
    |==========================================================================
    |
    | Control which models, relationships, and columns are accessible in reports.
    |
    | HOW IT WORKS:
    | 1. If 'included_*' is specified, ONLY those items are available
    | 2. Then 'excluded_*' is applied → removes items from the list
    | 3. Empty 'included_*' arrays → all items are available (default)
    |
    | TIP: Use inclusion for better security (explicit allowlist approach)
    */

    'included_models' => [
        // App\Models\User::class,
    ],

    'excluded_models' => [
        App\Models\Activity::class,
        App\Models\Admin::class,
        App\Models\Alert::class,
        App\Models\ApiLog::class,
        App\Models\ApiPurgeLog::class,
        App\Models\Backup::class,
        App\Models\Banner::class,
        App\Models\CalendarTag::class,
        App\Models\Comment::class,
        App\Models\Country::class,
        App\Models\DocumentTag::class,
        App\Models\Domain::class,
        App\Models\Element::class,
        App\Models\EventTag::class,
        App\Models\FormTag::class,
        App\Models\JobHistory::class,
        App\Models\Mail::class,
        App\Models\Metric::class,
        App\Models\ModelLike::class,
        App\Models\ModelNotification::class,
        App\Models\ModelTag::class,
        App\Models\PassportClient::class,
        App\Models\PassportToken::class,
        App\Models\Schedule::class,
        App\Models\Settings::class,
        App\Models\SocialiteUser::class,
        App\Models\Tag::class,
        App\Models\Tenant::class,
        App\Models\WebhookLog::class,
    ],

    'included_relationships' => [
        // 'user',
    ],

    // Relationships to hide from reports
    'excluded_relationships' => [
        // 'adminNotes',
    ],

    /*
    |==========================================================================
    | Column Visibility Control
    |==========================================================================
    |
    | Control which database columns are visible in reports.
    */

    'included_columns' => [
        'global' => [
            // 'id',
            // 'name',
        ],

        'models' => [
            // App\Models\User::class => ['id', 'name', 'email'],
        ],
    ],

    'excluded_columns' => [
        'global' => [
            'password',
            'remember_token',
            'two_factor_secret',
            'api_token',
        ],

        'models' => [
            // App\Models\User::class => ['social_security_number'],
        ],
    ],

    /*
    |==========================================================================
    | Feature Toggles
    |==========================================================================
    |
    | Control which features are available in your reports.
    */
    'features' => [
        'aggregation' => [
            'columns_enabled' => true,

            // Enable/disable aggregate filters
            'filters_enabled' => true,
        ],

        'sharing' => [
            // Enable/disable user-level sharing entirely
            'user_sharing_enabled' => true,
        ],
    ],

    /*
    |==========================================================================
    | Export Settings
    |==========================================================================
    */

    'exports' => [
        'enabled' => false,
        'formats' => ['csv', 'xlsx'],
        'default_format' => 'csv',
        'chunk_size' => 2000,
        'should_queue' => true,
        'queue' => env('DATA_LENS_QUEUE'),
    ],

    /*
    |==========================================================================
    | Report Scheduling (Email Distribution)
    |==========================================================================
    */

    'scheduling' => [
        'enabled' => false,
        'from_email' => env('MAIL_FROM_ADDRESS'),
        'from_name' => env('MAIL_FROM_NAME'),
        'queue' => env('DATA_LENS_QUEUE'),

        'max_attachment_size' => 1024, // KB
        'download_link_expiry_days' => 7,
        'max_recipients_per_schedule' => 50,
        'history_retention_days' => 30,
    ],

    /*
    |==========================================================================
    | Performance & Caching
    |==========================================================================
    */

    'cache' => [
        'enabled' => true,
        'force_in_local' => false,
        'prefix' => 'data_lens',

        'ttl' => [
            'model_fields' => 21600,        // 6 hours
            'model_relationships' => 21600, // 6 hours
            'relationship_type' => 21600,   // 6 hours
        ],
    ],

    /*
    |==========================================================================
    | Advanced Settings
    |==========================================================================
    |
    | These settings rarely need to be changed.
    */

    'eligible_relationships' => [
        BelongsTo::class,
        HasOne::class,
        HasOneThrough::class,
        MorphOne::class,
        HasMany::class,
        HasManyThrough::class,
        BelongsToMany::class,
    ],

    'column_type_detection' => [
        'money_field_patterns' => [
            'price', 'cost', 'amount', 'balance', 'fee', 'payment',
            'total', 'revenue', 'income', 'expense',
        ],
        'boolean_field_patterns' => [
            'is_', 'has_', 'can_', 'should_', 'active', 'enabled',
        ],
    ],

    'table_names' => [
        'custom_reports' => 'custom_reports',
        'custom_report_user' => 'custom_report_user',
        'custom_report_schedules' => 'custom_report_schedules',
        'custom_report_schedule_history' => 'custom_report_schedule_history',
        'custom_report_schedule_recipients' => 'custom_report_schedule_recipients',
    ],

    'column_names' => [
        'tenant_foreign_key' => 'tenant_id',
    ],

    'filename_templates' => [
        'exports' => 'report_{report_name}_{date}',
        'scheduling' => 'scheduled_{report_name}_{date}',
    ],

    'timezone' => [
        'default' => env('DATA_LENS_TIMEZONE', 'UTC'),
    ],

    'integrations' => [
        'custom_fields' => false,
        'advanced_tables' => false,
    ],

    'through_relationships' => [
        'max_depth' => 3,
        'optimize_queries' => true,
        'cache_ttl' => 43200, // 12 hours
    ],

    'mailable_classes' => [
        'report_email' => ReportEmail::class,
    ],
];

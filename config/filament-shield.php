<?php

declare(strict_types=1);

use App\Filament\App\Clusters\Settings;

return [
    'shield_resource' => [
        'should_register_navigation' => true,
        'slug' => 'roles',
        'navigation_sort' => 8,
        'navigation_badge' => false,
        'navigation_group' => true,
        'is_globally_searchable' => false,
        'show_model_path' => false,
        'is_scoped_to_tenant' => false,
        'cluster' => Settings::class,
    ],

    'auth_provider_model' => [
        'fqcn' => App\Models\User::class,
    ],

    'super_admin' => [
        'enabled' => true,
        'name' => 'Admin',
        'define_via_gate' => false,
        'intercept_gate' => 'before', // after
    ],

    'panel_user' => [
        'enabled' => false,
        'name' => 'User',
    ],

    'permission_prefixes' => [
        'resource' => [
            'view',
            'view_any',
            'create',
            'update',
            'restore',
            'restore_any',
            'replicate',
            'reorder',
            'delete',
            'delete_any',
            'force_delete',
            'force_delete_any',
        ],

        'page' => 'page',
        'widget' => 'widget',
    ],

    'entities' => [
        'pages' => true,
        'widgets' => false,
        'resources' => true,
        'custom_permissions' => false,
    ],

    'generator' => [
        'option' => 'policies_and_permissions',
        'policy_directory' => 'Policies',
        'policy_namespace' => 'Policies',
    ],

    'exclude' => [
        'enabled' => true,

        'pages' => [
            'Dashboard',
            'Integration',
            'Logs',
            'Organization',
            'Permission',
            'Registration',
        ],

        'widgets' => [
            'AccountWidget', 'FilamentInfoWidget',
        ],

        'resources' => [],
    ],

    'discovery' => [
        'discover_all_resources' => false,
        'discover_all_widgets' => false,
        'discover_all_pages' => false,
    ],

    'register_role_policy' => [
        'enabled' => false,
    ],

];

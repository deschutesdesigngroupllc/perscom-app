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

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    |
    | The available scopes for the API.
    |
    */

    'scopes' => [
        'view:announcement' => 'Can view an announcement',
        'create:announcement' => 'Can create an announcement',
        'update:announcement' => 'Can update an announcement',
        'delete:announcement' => 'Can delete an announcement',
        'view:assignmentrecord' => 'Can view an assignment record',
        'create:assignmentrecord' => 'Can create an assignment record',
        'update:assignmentrecord' => 'Can update an assignment record',
        'delete:assignmentrecord' => 'Can delete an assignment record',
        'view:attachment' => 'Can view an attachment',
        'create:attachment' => 'Can create an attachment',
        'update:attachment' => 'Can update an attachment',
        'delete:attachment' => 'Can delete an attachment',
        'view:award' => 'Can view an award',
        'create:award' => 'Can create an award',
        'update:award' => 'Can update an award',
        'delete:award' => 'Can delete an award',
        'view:awardrecord' => 'Can view an award record',
        'create:awardrecord' => 'Can create an award record',
        'update:awardrecord' => 'Can update an award record',
        'delete:awardrecord' => 'Can delete an award record',
        'clear:cache' => 'Can clear the cache',
        'view:calendar' => 'Can view a calendar',
        'create:calendar' => 'Can create a calendar',
        'update:calendar' => 'Can update a calendar',
        'delete:calendar' => 'Can delete a calendar',
        'view:category' => 'Can view a category',
        'create:category' => 'Can create a category',
        'update:category' => 'Can update a category',
        'delete:category' => 'Can delete a category',
        'view:combatrecord' => 'Can view a combat record',
        'create:combatrecord' => 'Can create a combat record',
        'update:combatrecord' => 'Can update a combat record',
        'delete:combatrecord' => 'Can delete a combat record',
        'view:comment' => 'Can view a comment',
        'create:comment' => 'Can create a comment',
        'update:comment' => 'Can update a comment',
        'delete:comment' => 'Can delete a comment',
        'view:competency' => 'Can view a competency',
        'create:competency' => 'Can create a competency',
        'update:competency' => 'Can update a competency',
        'delete:competency' => 'Can delete a competency',
        'view:credential' => 'Can view a credential',
        'create:credential' => 'Can create a credential',
        'update:credential' => 'Can update a credential',
        'delete:credential' => 'Can delete a credential',
        'view:document' => 'Can view a document',
        'create:document' => 'Can create a document',
        'update:document' => 'Can update a document',
        'delete:document' => 'Can delete a document',
        'view:event' => 'Can view an event',
        'create:event' => 'Can create an event',
        'update:event' => 'Can update an event',
        'delete:event' => 'Can delete an event',
        'view:field' => 'Can view a field',
        'create:field' => 'Can create a field',
        'update:field' => 'Can update a field',
        'delete:field' => 'Can delete a field',
        'view:form' => 'Can view a form',
        'create:form' => 'Can create a form',
        'update:form' => 'Can update a form',
        'delete:form' => 'Can delete a form',
        'view:group' => 'Can view a group',
        'create:group' => 'Can create a group',
        'update:group' => 'Can update a group',
        'delete:group' => 'Can delete a group',
        'view:image' => 'Can view an image',
        'create:image' => 'Can create an image',
        'update:image' => 'Can update an image',
        'delete:image' => 'Can delete an image',
        'view:issuer' => 'Can view an issuer',
        'create:issuer' => 'Can create an issuer',
        'update:issuer' => 'Can update an issuer',
        'delete:issuer' => 'Can delete an issuer',
        'view:message' => 'Can view a message',
        'create:message' => 'Can create a message',
        'update:message' => 'Can update a message',
        'delete:message' => 'Can delete a message',
        'view:newsfeed' => 'Can view a newsfeed item',
        'create:newsfeed' => 'Can create a newsfeed item',
        'update:newsfeed' => 'Can update a newsfeed item',
        'delete:newsfeed' => 'Can delete a newsfeed item',
        'view:position' => 'Can view a position',
        'create:position' => 'Can create a position',
        'update:position' => 'Can update a position',
        'delete:position' => 'Can delete a position',
        'view:qualification' => 'Can view a qualification',
        'create:qualification' => 'Can create a qualification',
        'update:qualification' => 'Can update a qualification',
        'delete:qualification' => 'Can delete a qualification',
        'view:qualificationrecord' => 'Can view a qualification record',
        'create:qualificationrecord' => 'Can create a qualification record',
        'update:qualificationrecord' => 'Can update a qualification record',
        'delete:qualificationrecord' => 'Can delete a qualification record',
        'view:rank' => 'Can view a rank',
        'create:rank' => 'Can create a rank',
        'update:rank' => 'Can update a rank',
        'delete:rank' => 'Can delete a rank',
        'view:rankrecord' => 'Can view a rank record',
        'create:rankrecord' => 'Can create a rank record',
        'update:rankrecord' => 'Can update a rank record',
        'delete:rankrecord' => 'Can delete a rank record',
        'view:servicerecord' => 'Can view a service record',
        'create:servicerecord' => 'Can create a service record',
        'update:servicerecord' => 'Can update a service record',
        'delete:servicerecord' => 'Can delete a service record',
        'view:settings' => 'Can view application settings',
        'view:slot' => 'Can view a slot',
        'create:slot' => 'Can create a slot',
        'update:slot' => 'Can update a slot',
        'delete:slot' => 'Can delete a slot',
        'view:specialty' => 'Can view a specialty',
        'create:specialty' => 'Can create a specialty',
        'update:specialty' => 'Can update a specialty',
        'delete:specialty' => 'Can delete a specialty',
        'view:status' => 'Can view a status',
        'create:status' => 'Can create a status',
        'update:status' => 'Can update a status',
        'delete:status' => 'Can delete a status',
        'view:statusrecord' => 'Can view a status record',
        'create:statusrecord' => 'Can create a status record',
        'update:statusrecord' => 'Can update a status record',
        'delete:statusrecord' => 'Can delete a status record',
        'view:submission' => 'Can view a form submission',
        'create:submission' => 'Can create a form submission',
        'update:submission' => 'Can update a form submission',
        'delete:submission' => 'Can delete a form submission',
        'view:task' => 'Can view a task',
        'create:task' => 'Can create a task',
        'update:task' => 'Can update a task',
        'delete:task' => 'Can delete a task',
        'view:trainingrecord' => 'Can view a training record',
        'create:trainingrecord' => 'Can create a training record',
        'update:trainingrecord' => 'Can update a training record',
        'delete:trainingrecord' => 'Can delete a training record',
        'view:unit' => 'Can view a unit',
        'create:unit' => 'Can create a unit',
        'update:unit' => 'Can update a unit',
        'delete:unit' => 'Can delete a unit',
        'view:user' => 'Can view a user',
        'create:user' => 'Can create a user',
        'update:user' => 'Can update a user',
        'delete:user' => 'Can delete a user',
    ],
];

<?php

return [

    /*
    |--------------------------------------------------------------------------
    | OpenAI API Key and Organization
    |--------------------------------------------------------------------------
    |
    | Here you may specify your OpenAI API Key and organization. This will be
    | used to authenticate with the OpenAI API - you can find your API key
    | and organization on your OpenAI dashboard, at https://openai.com.
    */

    'api_key' => env('OPENAI_API_KEY'),
    'organization' => env('OPENAI_ORGANIZATION'),
    'max_tokens' => env('OPENAI_MAX_TOKENS', 50),
    'temperature' => env('OPENAI_TEMPERATURE', 0.2),

    /*
    |--------------------------------------------------------------------------
    | Request Timeout
    |--------------------------------------------------------------------------
    |
    | The timeout may be used to specify the maximum number of seconds to wait
    | for a response. By default, the client will time out after 30 seconds.
    */

    'request_timeout' => env('OPENAI_REQUEST_TIMEOUT', 30),

    /*
    |--------------------------------------------------------------------------
    | Prompts
    |--------------------------------------------------------------------------
    |
    | Returns prompts for the completions models to be used in model events.
    */

    'prompts' => [
        'awardrecord' => [
            'created' => [
                'headline' => 'Generate a one sentence headline about {user} being given a new award. Use the following text, award name, and award description from the award record for assistance. Award Record Text: {text} | Award Name: {award} | Award Description: {description}',
                'text' => 'Generate a one sentence summary about {user} being given a new award. Use the following text, award name, and award description from the award record for assistance. Award Record Text: {text} | Award Name: {award} | Award Description: {description}',
            ],
        ],
        'combatrecord' => [
            'created' => [
                'headline' => 'Generate a one sentence headline about {user} being assigned a new combat record. Use the following text from the combat record for assistance. Combat Record Text: {text}',
                'text' => 'Generate a one sentence summary about {user} being assigned a new combat record. Use the following text from the combat record for assistance. Combat Record Text: {text}',
            ],
        ],
        'qualificationrecord' => [
            'created' => [
                'headline' => 'Generate a one sentence headline about {user} receiving a new qualification. Use the following text, qualification name, and qualification description from the qualification record for assistance. Qualification Record Text: {text} | Qualification Name: {qualification} | Qualification Description: {description}',
                'text' => 'Generate a one sentence summary about {user} receiving a new qualification. Use the following text, qualification name, and qualification description from the qualification record for assistance. Qualification Record Text: {text} | Qualification Name: {qualification} | Qualification Description: {description}',
            ],
        ],
        'rankrecord' => [
            'created' => [
                'headline' => 'Generate a one sentence headline about {user} receiving a new rank. Use the following text, rank name, and rank description from the rank record for assistance. Rank Record Text: {text} | Rank Name: {rank} | Rank Description: {description}',
                'summary' => 'Generate a one sentence summary about {user} receiving a new rank. Use the following text, rank name, and rank description from the rank record for assistance. Rank Record Text: {text} | Rank Name: {rank} | Rank Description: {description}',
            ],
        ],
        'servicerecord' => [
            'created' => [
                'headline' => 'Generate a one sentence headline about {user} being assigned a new service record. Use the following text from the service record for assistance. Service Record Text: {text}',
                'text' => 'Generate a one sentence summary about {user} being assigned a new service record. Use the following text from the service record for assistance. Service Record Text: {text}',
            ],
        ],
    ],
];

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
    'max_tokens' => env('OPENAI_MAX_TOKENS', 200),
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
        'assignmentrecord' => [
            'created' => [
                'headline' => 'Generate a headline about a new assignment record being added to {user} personnel file. Use the following text, unit name, unit description, position name, position description, specialty name, and specialty description from the assignment record for assistance. Assignment Record Text: """{text}""" Unit Name: """{unit}""" Unit Description: """{unit_description}""" Position Name: """{position}""" Position Description: """{position_description}""" Specialty Name: """{specialty}""" Specialty Description: """{specialty_description}"""',
                'text' => 'Generate a very short summary about a new assignment record being added to {user} personnel file. Use the following text, unit name, unit description, position name, position description, specialty name, and specialty description from the assignment record for assistance. Assignment Record Text: """{text}""" Unit Name: """{unit}""" Unit Description: """{unit_description}""" Position Name: """{position}""" Position Description: """{position_description}""" Specialty Name: """{specialty}""" Specialty Description: """{specialty_description}"""',
            ],
        ],
        'awardrecord' => [
            'created' => [
                'headline' => 'Generate a headline about a new award record being added to {user} personnel file. Use the following text, award name, and award description from the award record for assistance. Award Record Text: """{text}""" Award Name: """{award}""" Award Description: """{description}"""',
                'text' => 'Generate a very short summary about a new award record being added to {user} personnel file. Use the following text, award name, and award description from the award record for assistance. Award Record Text: """{text}""" Award Name: """{award}""" Award Description: """{description}"""',
            ],
        ],
        'combatrecord' => [
            'created' => [
                'headline' => 'Generate a headline about a new combat record being added to {user} personnel file. Use the following text from the combat record for assistance. Combat Record Text: """{text}"""',
                'text' => 'Generate a very short summary about a new combat record being added to {user} personnel file. Use the following text from the combat record for assistance. Combat Record Text: """{text}"""',
            ],
        ],
        'qualificationrecord' => [
            'created' => [
                'headline' => 'Generate a headline about a new qualification record being added to {user} personnel file. Use the following text, qualification name, and qualification description from the qualification record for assistance. Qualification Record Text: """{text}""" Qualification Name: """{qualification}""" Qualification Description: """{description}"""',
                'text' => 'Generate a very short summary about a new qualification record being added to {user} personnel file. Use the following text, qualification name, and qualification description from the qualification record for assistance. Qualification Record Text: """{text}""" Qualification Name: """{qualification}""" Qualification Description: """{description}"""',
            ],
        ],
        'rankrecord' => [
            'created' => [
                'headline' => 'Generate a headline about a new rank record being added to {user} personnel file. Use the following text, rank name, and rank description from the rank record for assistance. Rank Record Text: """{text}""" Rank Name: """{rank}""" Rank Description: """{description}"""',
                'text' => 'Generate a very short summary about a new rank record being added to {user} personnel file. Use the following text, rank name, and rank description from the rank record for assistance. Rank Record Text: """{text}""" Rank Name: """{rank}""" Rank Description: """{description}"""',
            ],
        ],
        'servicerecord' => [
            'created' => [
                'headline' => 'Generate a headline about a new service record being added to {user} personnel file. Use the following text from the service record for assistance. Service Record Text: """{text}"""',
                'text' => 'Generate a very short summary about a new service record being added to {user} personnel file. Use the following text from the service record for assistance. Service Record Text: """{text}"""',
            ],
        ],
    ],
];

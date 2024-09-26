<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'whatagraph' => [
        'token' => env('WHATAGRAPH_TOKEN'),
        'endpoint' => env('WHATAGRAPH_BASE_ENDPOINT'),
        'account' => env('WHATAGRAPH_ACCOUNT'),
    ],
    'openweathermap' => [
        'data_endpoint' => env('OPENWEATHERMAP_DATA_ENDPOINT'),
        'geo_endpoint' => env('OPENWEATHERMAP_GEO_ENDPOINT'),
        'token' => env('OPENWEATHERMAP_TOKEN'),
    ],


    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

];

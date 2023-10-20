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

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'jira' => [
        'baseUrl' => env('JIRA_BASE_URL'),
        'jiraProject' => env('JIRA_PROJECT'),
        'baseApiUrlV3' => env('JIRA_BASE_API_URL_V3'),
        'baseApiUrlV2' => env('JIRA_BASE_API_URL_V2'),
        'token' => env('JIRA_TOKEN'),
        'username' => env('JIRA_USERNAME'),
    ],

    'trujay' => [
        'baseApiUrl' => env('TRUJAY_BASE_API_URL'),
        'userKey' => env('TRUJAY_USER_KEY'),
    ],
];

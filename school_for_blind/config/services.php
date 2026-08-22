<?php

use App\Models\User;

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

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],
    'stripe' => [
    'model' => 'App\\Models\\User',
    'key' => 'pk_test_51TkNlpHb6vvSAewAX80goBxYkrKudKIvMMUJWCWUtfBtz34M6b7nSkK856iN4VelOTa5A691otg1gYWZ7NU0GhCn00M54djgTI',
    'secret' => 'sk_test_51TkNlpHb6vvSAewACYwFA6BtFIXEJA8myXILf2xfAvVhMtuxMXOGq78DkKkqJd3YMgreEGdm9hQQrrXaQTYqryEB00FaNh1NhH',
    'webhook_secret' => 'whsec_388f7edf69ba8826b3120a184e4829da67bc0c8f505eb30cdee2343791d0a99f',
],

'cashier' => [
    'key' => 'pk_test_51TkNlpHb6vvSAewAX80goBxYkrKudKIvMMUJWCWUtfBtz34M6b7nSkK856iN4VelOTa5A691otg1gYWZ7NU0GhCn00M54djgTI',
    'secret' => 'sk_test_51TkNlpHb6vvSAewACYwFA6BtFIXEJA8myXILf2xfAvVhMtuxMXOGq78DkKkqJd3YMgreEGdm9hQQrrXaQTYqryEB00FaNh1NhH',
    'webhook_secret' => 'whsec_388f7edf69ba8826b3120a184e4829da67bc0c8f505eb30cdee2343791d0a99f',
],
];

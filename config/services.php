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

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
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

    'whatsapp' => [
        'enabled' => env('WHATSAPP_ENABLED', false),
        'fonnte_url' => env('WHATSAPP_FONNTE_URL', 'https://api.fonnte.com/send'),
        'fonnte_key' => env('WHATSAPP_FONNTE_KEY', ''),
        // Sender WhatsApp number (the registered Fonnte device). Used for
        // reference/logging; Fonnte resolves the sender from the token above.
        'sender' => env('WHATSAPP_SENDER_NUMBER'),
        // Verifikasi SSL saat memanggil Fonnte. Biarkan true di produksi (cPanel).
        // Set false HANYA untuk dev lokal (Windows/XAMPP) yang belum punya CA bundle,
        // yang memunculkan "cURL error 60: SSL certificate problem".
        'verify_ssl' => env('WHATSAPP_VERIFY_SSL', true),
    ],

];

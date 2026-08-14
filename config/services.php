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

    'facebook' => [
        // App ID từ https://developers.facebook.com/apps — bắt buộc để hết cảnh báo fb:app_id
        'app_id' => env('FACEBOOK_APP_ID', ''),
    ],

    /*
    |--------------------------------------------------------------------------
    | Sapo Private App (Basic Auth)
    |--------------------------------------------------------------------------
    | Docs: https://support.sapo.vn — API Reference
    | Auth: https://apikey:apisecret@{store}/admin/{resource}.json
    */
    'sapo' => [
        'enabled' => filter_var(env('SAPO_ENABLED', false), FILTER_VALIDATE_BOOLEAN),
        'store' => env('SAPO_STORE', ''),
        'api_key' => env('SAPO_API_KEY', ''),
        'api_secret' => env('SAPO_API_SECRET', ''),
        'access_token' => env('SAPO_ACCESS_TOKEN', ''),
        'timeout' => (int) env('SAPO_TIMEOUT', 60),
        // GET /admin/products.json?product_type=... (Sapo dùng số ít)
        'product_type' => env('SAPO_PRODUCT_TYPE', 'Đồ chơi'),
        // Không GET all mỗi request: cache local + modified_on_min từ last_fetch_api_sapo (UTC)
        'sync_min_interval' => (int) env('SAPO_SYNC_MIN_INTERVAL', 60),
        // local = storefront đọc bảng product; sapo = đọc cache API
        'storefront_source' => env('SAPO_STOREFRONT_SOURCE', 'local'),
    ],

];

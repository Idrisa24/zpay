<?php

use Saidtech\Zpay\Zpay;

return [

    'name' => env('APP_NAME', 'Zpay'),

    'stateful' => explode(',', env('ZPAY_DOMAIN', sprintf(
        '%s%s',
        'openapi.m-pesa.com',
        Zpay::currentApplicationUrlWithPort()
    ))),

    /*
    |--------------------------------------------------------------------------
    | Zpay Guards
    |--------------------------------------------------------------------------
    |
    | This array contains the authentication guards that will be checked when
    | Zpay is trying to authenticate a request. If none of these guards
    | are able to authenticate the request, Zpay will use the bearer
    | token that's present on an incoming request for authentication.
    |
    */

    'debug_env' => env('MPESA_ENV', 'sandbox'),

    'guard' => ['web'],

    'prefix' => 'zpay',

    'url' => env('MPESA_URL', 'openapi.m-pesa.com'),

    'call_back_url' => env('CALL_BACK_URL', env('APP_URL')),

    // 'asset_url' => env('ASSET_URL'),
    'token' => env('MPESA_TOKEN'),
    'api_key' => env('MPESA_API_KEY'),
    'country' => env('MPESA_COUNTRY', 'TZN'),
    'currency' => env('MPESA_CURRENCY', 'TZS'),
    'origin'=>'',
    'session_url'=>'/ipg/v2/vodacomTZN/getSession/',
    'transaction_url'=>'/ipg/v2/',
    'service_code' => env('MPESA_SERVICE_CODE','000000'),

    /*
    |--------------------------------------------------------------------------
    | Expiration Minutes
    |--------------------------------------------------------------------------
    |
    | This value controls the number of minutes until an issued token will be
    | considered expired. If this value is null, personal access tokens do
    | not expire. This won't tweak the lifetime of first-party sessions.
    |
    */

    'expiration' => null,

    /*
    |--------------------------------------------------------------------------
    | Zpay Middleware
    |--------------------------------------------------------------------------
    |
    | When authenticating your first-party SPA with Zpay you may need to
    | customize some of the middleware Zpay uses while processing the
    | request. You may change the middleware listed below as required.
    |
    */

    'middleware' => [
        'verify_csrf_token' => App\Http\Middleware\VerifyCsrfToken::class,
        'encrypt_cookies' => App\Http\Middleware\EncryptCookies::class,
    ],

];

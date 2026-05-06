<?php

return [

    /*
    |--------------------------------------------------------------------------
    | OnePay Card Gateway
    |--------------------------------------------------------------------------
    |
    | Sandbox endpoint and credentials provided by OnePay.
    | card_mode: international | domestic | both
    |
    */

    'sandbox' => (bool) env('ONEPAY_SANDBOX', true),
    'card_mode' => env('ONEPAY_CARD_MODE', 'both'),
    'locale' => env('ONEPAY_LOCALE', 'vn'),
    'currency' => env('ONEPAY_CURRENCY', 'VND'),

    'merchant' => env('ONEPAY_MERCHANT'),
    'access_code' => env('ONEPAY_ACCESS_CODE'),
    'hash_key' => env('ONEPAY_HASH_KEY'),
    'user' => env('ONEPAY_USER'),
    'password' => env('ONEPAY_PASSWORD'),

    'return_url' => env('ONEPAY_RETURN_URL'),
    'ipn_url' => env('ONEPAY_IPN_URL'),

];

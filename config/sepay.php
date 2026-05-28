<?php

return [

    /*
    |--------------------------------------------------------------------------
    | SePay QR (qr.sepay.vn)
    |--------------------------------------------------------------------------
    |
    | See: https://developer.sepay.vn/vi/sepay-webhooks/tao-qr-va-form-thanh-toan
    |
    */

    'qr' => [
        'base_url' => env('SEPAY_QR_BASE_URL', 'https://qr.sepay.vn/img'),
        'bank_code' => env('SEPAY_QR_BANK_CODE'),
        'bank_display_name' => env('SEPAY_QR_BANK_NAME'),
        'account_number' => env('SEPAY_QR_ACCOUNT_NUMBER'),
        'account_name' => env('SEPAY_QR_ACCOUNT_NAME'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Webhook (incoming from SePay)
    |--------------------------------------------------------------------------
    |
    | SePay sends: Authorization: Apikey {key} when API Key auth is enabled.
    |
    */

    'webhook' => [
        'api_key' => env('SEPAY_WEBHOOK_API_KEY'),
    ],

];

<?php

return [
    'moderation' => [
        'enabled' => env('AI_MODERATION_ENABLED', true),
        'auto_approve_threshold' => (float) env('AI_AUTO_APPROVE_THRESHOLD', 0.93),
        'auto_reject_threshold' => (float) env('AI_AUTO_REJECT_THRESHOLD', 0.90),
        'max_attempts' => (int) env('AI_REVIEW_MAX_ATTEMPTS', 3),
        'timeout' => (int) env('AI_REVIEW_TIMEOUT', 20),
    ],

    'gemini' => [
        'api_key' => env('GOOGLE_AI_API_KEY'),
        'model' => env('GEMINI_MODEL', 'gemini-1.5-flash'),
        'base_uri' => env('GEMINI_BASE_URI', 'https://generativelanguage.googleapis.com/v1beta/'),
    ],
];

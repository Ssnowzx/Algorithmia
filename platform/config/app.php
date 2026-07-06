<?php

declare(strict_types=1);

return [
    'name' => env('APP_NAME', 'Algorithmia Platform'),
    'env' => env('APP_ENV', 'production'),
    'debug' => (bool) env('APP_DEBUG', false),
    'url' => env('APP_URL', 'http://localhost'),
    'timezone' => env('APP_TIMEZONE', 'UTC'),
    'locale' => env('APP_LOCALE', 'pt_BR'),
    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'en'),
    'faker_locale' => env('APP_FAKER_LOCALE', 'pt_BR'),
    'cipher' => 'AES-256-CBC',
    'key' => env('APP_KEY'),
];

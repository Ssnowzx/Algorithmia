<?php

declare(strict_types=1);

return [
    'default' => env('CACHE_STORE', 'redis'),
    'stores' => [
        'redis' => [
            'driver' => 'redis',
            'connection' => 'cache',
        ],
    ],
    'prefix' => env('CACHE_PREFIX', 'algorithmia_platform_cache'),
];

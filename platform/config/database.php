<?php
declare(strict_types=1);

return [
    'default' => env('DB_CONNECTION', 'pgsql'),
    'connections' => [
        'pgsql' => [
            'driver' => 'pgsql',
            'host' => env('DB_HOST', 'postgres'),
            'port' => (int) env('DB_PORT', 5432),
            'database' => env('DB_DATABASE', 'algorithmia_platform'),
            'username' => env('DB_USERNAME', 'algorithmia'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
            'search_path' => 'public',
            'sslmode' => 'prefer',
        ],
    ],
    'migrations' => 'migrations',
    'redis' => [
        'client' => env('REDIS_CLIENT', 'phpredis'),
        'default' => [
            'host' => env('REDIS_HOST', 'redis'),
            'password' => env('REDIS_PASSWORD'),
            'port' => (int) env('REDIS_PORT', 6379),
            'database' => (int) env('REDIS_DB', 0),
        ],
        'cache' => [
            'host' => env('REDIS_HOST', 'redis'),
            'password' => env('REDIS_PASSWORD'),
            'port' => (int) env('REDIS_PORT', 6379),
            'database' => (int) env('REDIS_CACHE_DB', 1),
        ],
    ],
];

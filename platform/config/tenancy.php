<?php

declare(strict_types=1);

$platformHosts = array_values(array_filter(array_map(
    static fn (string $host): string => strtolower(trim($host)),
    explode(',', (string) env('TENANCY_PLATFORM_HOSTS', 'localhost,127.0.0.1,algorithmia.test')),
)));

return [
    'platform_hosts' => $platformHosts,
    'trusted_proxies' => array_values(array_filter(array_map(
        static fn (string $proxy): string => trim($proxy),
        explode(',', (string) env('TENANCY_TRUSTED_PROXIES', '')),
    ))),
];

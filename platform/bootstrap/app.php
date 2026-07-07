<?php

declare(strict_types=1);

use App\Domain\Tenancy\Http\Middleware\ResolveTenantFromHost;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        apiPrefix: '',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $trustedProxies = array_values(array_filter(array_map(
            static fn (string $proxy): string => trim($proxy),
            explode(',', (string) env('TENANCY_TRUSTED_PROXIES', '')),
        )));

        $middleware->trustProxies(at: $trustedProxies);

        $middleware->alias([
            'resolve.tenant' => ResolveTenantFromHost::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();

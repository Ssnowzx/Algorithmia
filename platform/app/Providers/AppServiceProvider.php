<?php

declare(strict_types=1);

namespace App\Providers;

use App\Domain\Tenancy\CurrentTenant;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

final class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        RateLimiter::for('tenant-login', function (Request $request): Limit {
            if (app()->bound(CurrentTenant::class)) {
                /** @var CurrentTenant $currentTenant */
                $currentTenant = app(CurrentTenant::class);
                $tenantId = $currentTenant->tenantId;
            } else {
                $tenantId = 'global';
            }

            $normalizedEmail = mb_strtolower(trim((string) $request->input('email', '')));

            return Limit::perMinute(5)->by(sprintf(
                '%s|%s|%s',
                $tenantId,
                (string) $request->ip(),
                hash('sha256', $normalizedEmail),
            ));
        });
    }
}

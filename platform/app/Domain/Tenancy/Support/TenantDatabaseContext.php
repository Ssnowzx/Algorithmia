<?php

declare(strict_types=1);

namespace App\Domain\Tenancy\Support;

use App\Domain\Tenancy\CurrentTenant;
use Closure;
use Illuminate\Support\Facades\DB;

final class TenantDatabaseContext
{
    /**
     * @template TReturn
     *
     * @param  Closure():TReturn  $callback
     * @return TReturn
     */
    public function run(CurrentTenant $currentTenant, Closure $callback)
    {
        return DB::connection('pgsql')->transaction(static function () use ($currentTenant, $callback) {
            DB::connection('pgsql')->selectOne(
                "select set_config('app.tenant_id', ?, true) as tenant_id",
                [$currentTenant->tenantId],
            );

            return $callback();
        });
    }
}

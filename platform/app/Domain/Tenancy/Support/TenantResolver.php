<?php

declare(strict_types=1);

namespace App\Domain\Tenancy\Support;

use App\Domain\Tenancy\CurrentTenant;
use Illuminate\Support\Facades\DB;

final class TenantResolver
{
    public function resolve(string $normalizedHost): ?CurrentTenant
    {
        $result = DB::connection('pgsql')->selectOne(
            'select public.resolve_active_tenant(?) as tenant_id',
            [$normalizedHost],
        );

        if ($result === null || ! isset($result->tenant_id) || $result->tenant_id === null) {
            return null;
        }

        return new CurrentTenant((string) $result->tenant_id, $normalizedHost);
    }
}

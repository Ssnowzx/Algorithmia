<?php

declare(strict_types=1);

namespace App\Domain\Tenancy\Http\Controllers;

use App\Domain\Tenancy\CurrentTenant;
use App\Domain\Tenancy\Models\TenantMembership;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

final class TenantContextController
{
    public function __invoke(CurrentTenant $currentTenant): JsonResponse
    {
        DB::connection('pgsql')->selectOne("select current_setting('app.tenant_id', true) as tenant_id");
        TenantMembership::query()->count();

        return response()->json([
            'status' => 'ok',
        ]);
    }
}

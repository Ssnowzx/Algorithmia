<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Domain\Tenancy\CurrentTenant;
use App\Domain\Tenancy\Models\Tenant;
use App\Domain\Tenancy\Models\TenantDomain;
use App\Domain\Tenancy\Support\TenantResolver;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;
use Tests\TestCase;

final class TenantResolverTest extends TestCase
{
    public function test_it_resolves_a_tenant_for_an_active_domain(): void
    {
        $tenant = $this->createTenant(status: 'active');
        $domain = $this->createDomain($tenant, 'tenant-'.Str::uuid()->toString().'.example.test', true);

        $resolved = app(TenantResolver::class)->resolve($domain->host);

        self::assertInstanceOf(CurrentTenant::class, $resolved);
        self::assertSame($tenant->id, $resolved->tenantId);
        self::assertSame($domain->host, $resolved->host);
    }

    public function test_it_returns_null_for_unknown_domains(): void
    {
        $resolved = app(TenantResolver::class)->resolve('unknown-'.Str::uuid()->toString().'.example.test');

        self::assertNull($resolved);
    }

    public function test_it_returns_null_for_inactive_domains(): void
    {
        $tenant = $this->createTenant();
        $domain = $this->createDomain($tenant, 'inactive-'.Str::uuid()->toString().'.example.test', false);

        self::assertNull(app(TenantResolver::class)->resolve($domain->host));
    }

    public function test_it_returns_null_for_suspended_tenants(): void
    {
        $tenant = $this->createTenant(status: 'suspended');
        $domain = $this->createDomain($tenant, 'suspended-'.Str::uuid()->toString().'.example.test', true);

        self::assertNull(app(TenantResolver::class)->resolve($domain->host));
    }

    public function test_it_resolves_the_active_tenant_through_the_sql_function(): void
    {
        $tenant = $this->createTenant();
        $domain = $this->createDomain($tenant, 'function-'.Str::uuid()->toString().'.example.test', true);

        $result = DB::connection('pgsql')->selectOne(
            'select public.resolve_active_tenant(?) as tenant_id',
            [$domain->host],
        );

        self::assertNotNull($result);
        /** @var object{tenant_id: string|null} $result */
        self::assertSame($tenant->id, $result->tenant_id);
    }

    public function test_runtime_role_is_not_superuser_and_does_not_bypass_rls(): void
    {
        $result = DB::connection('pgsql')->selectOne(
            'select rolsuper, rolbypassrls, rolcreaterole, rolcreatedb from pg_roles where rolname = current_user',
        );

        if ($result === null) {
            throw new RuntimeException('Unable to inspect the runtime database role.');
        }

        /** @var object{rolsuper: bool, rolbypassrls: bool, rolcreaterole: bool, rolcreatedb: bool} $result */
        self::assertFalse((bool) $result->rolsuper);
        self::assertFalse((bool) $result->rolbypassrls);
        self::assertFalse((bool) $result->rolcreaterole);
        self::assertFalse((bool) $result->rolcreatedb);
    }

    private function createTenant(string $status = 'active'): Tenant
    {
        return Tenant::on('pgsql_migrator')->create([
            'name' => 'Tenant '.Str::uuid()->toString(),
            'slug' => 'tenant-'.Str::uuid()->toString(),
            'status' => $status,
        ]);
    }

    private function createDomain(Tenant $tenant, string $host, bool $isActive): TenantDomain
    {
        return TenantDomain::on('pgsql_migrator')->create([
            'tenant_id' => $tenant->id,
            'host' => $host,
            'is_primary' => true,
            'is_active' => $isActive,
        ]);
    }
}

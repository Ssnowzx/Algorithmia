<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Domain\Tenancy\CurrentTenant;
use App\Domain\Tenancy\Models\Tenant;
use App\Domain\Tenancy\Models\TenantDomain;
use App\Domain\Tenancy\Models\TenantMembership;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

final class TenantHttpBoundaryTest extends TestCase
{
    public function test_healthz_remains_global_without_tenant_context(): void
    {
        $this->assertTenantContextAbsent();

        $this->getJson('/healthz')
            ->assertOk()
            ->assertExactJson([
                'status' => 'ok',
                'checks' => [
                    'app' => 'ok',
                    'database' => 'ok',
                    'redis' => 'ok',
                ],
            ]);

        $this->assertTenantContextAbsent();
    }

    public function test_it_resolves_a_tenant_scoped_route_for_an_active_domain(): void
    {
        [$tenant, $domain] = $this->createTenantWithDomain('active');

        $this->withServerVariables(['HTTP_HOST' => $domain->host])
            ->getJson('/__tenant/context')
            ->assertOk()
            ->assertExactJson(['status' => 'ok']);

        $this->assertTenantContextAbsent();
    }

    public function test_it_returns_404_for_an_unknown_tenant_domain(): void
    {
        $this->withServerVariables(['HTTP_HOST' => 'missing-'.Str::uuid()->toString().'.example.test'])
            ->getJson('/__tenant/context')
            ->assertNotFound();
    }

    public function test_it_returns_404_for_an_inactive_tenant_domain(): void
    {
        [$tenant, $domain] = $this->createTenantWithDomain('active', false);

        $this->withServerVariables(['HTTP_HOST' => $domain->host])
            ->getJson('/__tenant/context')
            ->assertNotFound();
    }

    public function test_it_returns_404_for_a_suspended_tenant(): void
    {
        [$tenant, $domain] = $this->createTenantWithDomain('suspended');

        $this->withServerVariables(['HTTP_HOST' => $domain->host])
            ->getJson('/__tenant/context')
            ->assertNotFound();
    }

    public function test_it_returns_404_when_a_platform_host_hits_a_tenant_route(): void
    {
        $this->withServerVariables(['HTTP_HOST' => '127.0.0.1:8080'])
            ->getJson('/__tenant/context')
            ->assertNotFound();
    }

    public function test_it_returns_400_for_a_malformed_host(): void
    {
        $this->withServerVariables(['HTTP_HOST' => 'https://tenant.example.test'])
            ->getJson('/__tenant/context')
            ->assertStatus(400);
    }

    public function test_it_ignores_tenant_inputs_from_query_body_cookie_and_headers(): void
    {
        [, $domain] = $this->createTenantWithDomain('active');

        $this->withServerVariables(['HTTP_HOST' => $domain->host])
            ->withHeader('X-Tenant-ID', (string) Str::uuid())
            ->withHeader('X-Tenant', 'another-tenant')
            ->withHeader('X-Forwarded-Host', 'localhost')
            ->withCookie('tenant_id', (string) Str::uuid())
            ->getJson('/__tenant/context?tenant_id='.Str::uuid()->toString().'&tenant=foo&slug=bar')
            ->assertOk()
            ->assertExactJson(['status' => 'ok']);
    }

    public function test_it_does_not_accept_forwarded_host_without_trusted_proxy_configuration(): void
    {
        [, $domain] = $this->createTenantWithDomain('active');

        $this->withServerVariables(['HTTP_HOST' => 'localhost'])
            ->withHeader('X-Forwarded-Host', $domain->host)
            ->getJson('/__tenant/context')
            ->assertNotFound();
    }

    public function test_it_clears_context_between_consecutive_tenant_requests(): void
    {
        [, $domainA] = $this->createTenantWithDomain('active', true, 'tenant-a');
        [, $domainB] = $this->createTenantWithDomain('active', true, 'tenant-b');

        $this->withServerVariables(['HTTP_HOST' => $domainA->host])
            ->getJson('/__tenant/context')
            ->assertOk();

        $this->assertTenantContextAbsent();

        $this->withServerVariables(['HTTP_HOST' => $domainB->host])
            ->getJson('/__tenant/context')
            ->assertOk();

        $this->assertTenantContextAbsent();
    }

    private function assertTenantContextAbsent(): void
    {
        self::assertFalse(app()->bound(CurrentTenant::class));

        $result = DB::connection('pgsql')->selectOne("select current_setting('app.tenant_id', true) as tenant_id");

        self::assertNotNull($result);
        /** @var object{tenant_id: string|null} $result */
        self::assertTrue($result->tenant_id === null || $result->tenant_id === '');
    }

    /**
     * @return array{0: Tenant, 1: TenantDomain}
     */
    private function createTenantWithDomain(string $status, bool $isActive = true, string $prefix = 'tenant'): array
    {
        $tenant = Tenant::on('pgsql_migrator')->create([
            'name' => 'Tenant '.Str::uuid()->toString(),
            'slug' => $prefix.'-'.Str::uuid()->toString(),
            'status' => $status,
        ]);

        $domain = TenantDomain::on('pgsql_migrator')->create([
            'tenant_id' => $tenant->id,
            'host' => $prefix.'-'.Str::uuid()->toString().'.example.test',
            'is_primary' => true,
            'is_active' => $isActive,
        ]);

        $user = \App\Domain\Identity\Models\User::on('pgsql_migrator')->create([
            'name' => 'User '.Str::uuid()->toString(),
            'email' => 'user-'.Str::uuid()->toString().'@example.test',
            'password' => 'secret-password',
        ]);

        TenantMembership::on('pgsql_migrator')->create([
            'tenant_id' => $tenant->id,
            'user_id' => $user->id,
            'role' => 'owner',
            'status' => 'active',
        ]);

        return [$tenant, $domain];
    }
}

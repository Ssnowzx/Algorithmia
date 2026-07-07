<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Domain\Identity\Models\User;
use App\Domain\Tenancy\CurrentTenant;
use App\Domain\Tenancy\Http\Middleware\ResolveTenantFromHost;
use App\Domain\Tenancy\Models\Tenant;
use App\Domain\Tenancy\Models\TenantDomain;
use App\Domain\Tenancy\Models\TenantMembership;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;
use Symfony\Component\HttpFoundation\Response;
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

        $this->tenantJson($domain->host, '/__tenant/context')
            ->assertOk()
            ->assertExactJson(['status' => 'ok']);

        $this->assertTenantContextAbsent();
    }

    public function test_it_returns_404_for_an_unknown_tenant_domain(): void
    {
        $this->tenantJson('missing-'.Str::uuid()->toString().'.example.test', '/__tenant/context')
            ->assertNotFound();
    }

    public function test_it_returns_404_for_an_inactive_tenant_domain(): void
    {
        [$tenant, $domain] = $this->createTenantWithDomain('active', false);

        $this->tenantJson($domain->host, '/__tenant/context')
            ->assertNotFound();
    }

    public function test_it_returns_404_for_a_suspended_tenant(): void
    {
        [$tenant, $domain] = $this->createTenantWithDomain('suspended');

        $this->tenantJson($domain->host, '/__tenant/context')
            ->assertNotFound();
    }

    public function test_it_returns_404_when_a_platform_host_hits_a_tenant_route(): void
    {
        $this->tenantJson('127.0.0.1:8080', '/__tenant/context')
            ->assertNotFound();
    }

    public function test_it_returns_400_for_a_malformed_host(): void
    {
        $request = Request::create(
            '/__tenant/context',
            'GET',
            [],
            [],
            [],
            [
                'HTTP_HOST' => 'tenant.example.test:65536',
                'HTTP_ACCEPT' => 'application/json',
            ],
        );

        $response = app(ResolveTenantFromHost::class)->handle(
            $request,
            static fn () => response()->json(['status' => 'ok']),
        );

        self::assertSame(400, $response->getStatusCode());
    }

    public function test_it_ignores_tenant_inputs_from_query_body_cookie_and_headers(): void
    {
        [, $domain] = $this->createTenantWithDomain('active');

        $this->tenantJson(
            $domain->host,
            '/__tenant/context?tenant_id='.Str::uuid()->toString().'&tenant=foo&slug=bar',
            [
                'X-Tenant-ID' => (string) Str::uuid(),
                'X-Tenant' => 'another-tenant',
                'X-Forwarded-Host' => 'localhost',
            ],
            [
                'tenant_id' => (string) Str::uuid(),
            ],
        )
            ->assertOk()
            ->assertExactJson(['status' => 'ok']);
    }

    public function test_it_does_not_accept_forwarded_host_without_trusted_proxy_configuration(): void
    {
        [, $domain] = $this->createTenantWithDomain('active');

        $this->tenantJson(
            'localhost',
            '/__tenant/context',
            ['X-Forwarded-Host' => $domain->host],
        )
            ->assertNotFound();
    }

    public function test_it_clears_context_between_consecutive_tenant_requests(): void
    {
        [, $domainA] = $this->createTenantWithDomain('active', true, 'tenant-a');
        [, $domainB] = $this->createTenantWithDomain('active', true, 'tenant-b');

        $this->tenantJson($domainA->host, '/__tenant/context')
            ->assertOk();

        $this->assertTenantContextAbsent();

        $this->tenantJson($domainB->host, '/__tenant/context')
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
     * @param  array<string, string>  $headers
     * @param  array<string, string>  $cookies
     * @return TestResponse<Response>
     */
    private function tenantJson(string $host, string $uri, array $headers = [], array $cookies = []): TestResponse
    {
        $fullUri = sprintf('http://%s%s', $host, $uri);
        $server = [
            'HTTP_ACCEPT' => 'application/json',
        ];

        foreach ($headers as $name => $value) {
            $server['HTTP_'.strtoupper(str_replace('-', '_', $name))] = $value;
        }

        return $this->call('GET', $fullUri, [], $cookies, [], $server);
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

        $user = User::on('pgsql_migrator')->create([
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

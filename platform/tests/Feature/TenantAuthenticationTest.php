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
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

final class TenantAuthenticationTest extends TestCase
{
    public function test_it_returns_the_login_form_for_a_tenant_host(): void
    {
        [, $domain] = $this->createTenantWithDomain('active');

        $this->tenantHtml('GET', $domain->host, '/login')
            ->assertOk()
            ->assertSee('<form', false)
            ->assertSee('name="email"', false)
            ->assertSee('name="password"', false);
    }

    public function test_it_authenticates_a_user_with_an_active_membership(): void
    {
        [$tenant, $domain, $user] = $this->createTenantWithUserAndMembership('active');

        $this->tenantJson('POST', $domain->host, '/login', [
            'email' => $user->email,
            'password' => 'secret-password',
        ])
            ->assertOk()
            ->assertExactJson(['status' => 'ok']);

        $this->tenantJson('GET', $domain->host, '/__tenant/auth-context')
            ->assertOk()
            ->assertExactJson(['status' => 'ok']);

        $this->assertTenantContextAbsent();
    }

    public function test_it_authenticates_the_same_user_in_two_tenants_with_active_memberships(): void
    {
        [$tenantA, $domainA, $tenantB, $domainB, $user] = $this->createSharedUserGraph();

        $this->tenantJson('POST', $domainA->host, '/login', [
            'email' => $user->email,
            'password' => 'secret-password',
        ])
            ->assertOk();

        $this->tenantJson('GET', $domainA->host, '/__tenant/auth-context')
            ->assertOk();

        $this->tenantJson('POST', $domainB->host, '/login', [
            'email' => $user->email,
            'password' => 'secret-password',
        ])
            ->assertOk();

        $this->tenantJson('GET', $domainB->host, '/__tenant/auth-context')
            ->assertOk();

        $this->assertTenantContextAbsent();
    }

    public function test_it_rejects_login_when_the_user_only_belongs_to_another_tenant(): void
    {
        [, $domainA, $tenantB, $domainB, $user] = $this->createTenantAndForeignUserGraph();

        $this->tenantJson('POST', $domainB->host, '/login', [
            'email' => $user->email,
            'password' => 'secret-password',
        ])
            ->assertStatus(422)
            ->assertJsonPath('message', 'As credenciais informadas são inválidas.');

        $this->assertTenantContextAbsent();
    }

    public function test_it_rejects_login_with_invalid_password(): void
    {
        [, $domain, $user] = $this->createTenantWithUserAndMembership('active');

        $response = $this->tenantJson('POST', $domain->host, '/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonPath('message', 'As credenciais informadas são inválidas.');

        $this->assertTenantContextAbsent();
    }

    public function test_it_rejects_login_with_a_missing_email_record(): void
    {
        [, $domain] = $this->createTenantWithDomain('active');

        $response = $this->tenantJson('POST', $domain->host, '/login', [
            'email' => 'missing-'.Str::uuid()->toString().'@example.test',
            'password' => 'secret-password',
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonPath('message', 'As credenciais informadas são inválidas.');

        $this->assertTenantContextAbsent();
    }

    public function test_it_rejects_login_with_a_suspended_membership(): void
    {
        [$tenant, $domain, $user] = $this->createTenantWithUserAndMembership('active', 'suspended');

        $this->tenantJson('POST', $domain->host, '/login', [
            'email' => $user->email,
            'password' => 'secret-password',
        ])
            ->assertStatus(422)
            ->assertJsonPath('message', 'As credenciais informadas são inválidas.');

        $this->assertTenantContextAbsent();
    }

    public function test_it_rejects_login_for_a_suspended_tenant(): void
    {
        [$tenant, $domain, $user] = $this->createTenantWithUserAndMembership('suspended');

        $this->tenantJson('POST', $domain->host, '/login', [
            'email' => $user->email,
            'password' => 'secret-password',
        ])
            ->assertNotFound();

        $this->assertTenantContextAbsent();
    }

    public function test_it_rejects_login_for_a_global_host(): void
    {
        $this->tenantJson('POST', 'localhost', '/login', [
            'email' => 'user@example.test',
            'password' => 'secret-password',
        ])
            ->assertNotFound();
    }

    public function test_it_rejects_login_for_an_unknown_host(): void
    {
        $this->tenantJson('POST', 'missing-'.Str::uuid()->toString().'.example.test', '/login', [
            'email' => 'user@example.test',
            'password' => 'secret-password',
        ])
            ->assertNotFound();
    }

    public function test_it_blocks_tenant_auth_context_for_a_guest(): void
    {
        [, $domain] = $this->createTenantWithDomain('active');

        $this->tenantJson('GET', $domain->host, '/__tenant/auth-context')
            ->assertStatus(401);
    }

    public function test_it_returns_auth_context_for_an_authenticated_member(): void
    {
        [, $domain, $user] = $this->createTenantWithUserAndMembership('active');

        $this->tenantJson('POST', $domain->host, '/login', [
            'email' => $user->email,
            'password' => 'secret-password',
        ])
            ->assertOk();

        $this->tenantJson('GET', $domain->host, '/__tenant/auth-context')
            ->assertOk()
            ->assertExactJson(['status' => 'ok']);

        $this->assertTenantContextAbsent();
    }

    public function test_it_blocks_a_revoked_membership_on_the_next_request(): void
    {
        [$tenant, $domain, $user, $membership] = $this->createTenantWithUserAndMembershipRecord('active');

        $this->tenantJson('POST', $domain->host, '/login', [
            'email' => $user->email,
            'password' => 'secret-password',
        ])
            ->assertOk();

        TenantMembership::on('pgsql_migrator')
            ->whereKey($membership->id)
            ->update(['status' => 'suspended']);

        $this->tenantJson('GET', $domain->host, '/__tenant/auth-context')
            ->assertStatus(403);

        $this->assertTenantContextAbsent();
    }

    public function test_it_does_not_grant_access_in_another_tenant_with_the_same_session(): void
    {
        [$tenantA, $domainA, $tenantB, $domainB, $userA] = $this->createTenantAndForeignUserGraph();

        $loginResponse = $this->tenantJson('POST', $domainA->host, '/login', [
            'email' => $userA->email,
            'password' => 'secret-password',
        ]);

        $loginResponse->assertOk();

        $sessionCookie = $this->sessionCookieValue($loginResponse);

        $this->withCookie(config('session.cookie'), $sessionCookie)
            ->tenantJson('GET', $domainB->host, '/__tenant/auth-context')
            ->assertStatus(403);

        $this->assertTenantContextAbsent();
    }

    public function test_it_ignores_tenant_inputs_when_authenticating(): void
    {
        [, $domain, $user] = $this->createTenantWithUserAndMembership('active');

        $this->tenantJson(
            'POST',
            $domain->host,
            '/login?tenant_id='.Str::uuid()->toString().'&tenant=foo&slug=bar',
            [
                'email' => $user->email,
                'password' => 'secret-password',
                'tenant_id' => (string) Str::uuid(),
            ],
            [
                'X-Tenant-ID' => (string) Str::uuid(),
                'X-Tenant' => 'other',
                'X-Forwarded-Host' => 'localhost',
            ],
            [
                'tenant_id' => (string) Str::uuid(),
            ],
        )
            ->assertOk();

        $this->tenantJson('GET', $domain->host, '/__tenant/auth-context')
            ->assertOk();

        $this->assertTenantContextAbsent();
    }

    public function test_it_rotates_the_session_after_login(): void
    {
        [, $domain, $user] = $this->createTenantWithUserAndMembership('active');

        $initialResponse = $this->tenantHtml('GET', $domain->host, '/login');
        $initialSessionCookie = $this->sessionCookieValue($initialResponse);

        $loginResponse = $this->tenantJson('POST', $domain->host, '/login', [
            'email' => $user->email,
            'password' => 'secret-password',
        ]);

        $loginResponse->assertOk();

        $newSessionCookie = $this->sessionCookieValue($loginResponse);

        self::assertNotSame($initialSessionCookie, $newSessionCookie);
    }

    public function test_it_invalidates_the_session_after_logout(): void
    {
        [, $domain, $user] = $this->createTenantWithUserAndMembership('active');

        $this->tenantJson('POST', $domain->host, '/login', [
            'email' => $user->email,
            'password' => 'secret-password',
        ])
            ->assertOk();

        $logoutResponse = $this->tenantJson('POST', $domain->host, '/logout');

        $logoutResponse->assertOk()
            ->assertExactJson(['status' => 'ok']);

        $this->tenantJson('GET', $domain->host, '/__tenant/auth-context')
            ->assertStatus(401);

        $this->assertTenantContextAbsent();
    }

    public function test_it_protects_login_with_csrf(): void
    {
        [, $domain, $user] = $this->createTenantWithUserAndMembership('active');

        $this->tenantJson('POST', $domain->host, '/login', [
            'email' => $user->email,
            'password' => 'secret-password',
        ], [], [], false)
            ->assertStatus(419);
    }

    public function test_it_protects_logout_with_csrf(): void
    {
        [, $domain, $user] = $this->createTenantWithUserAndMembership('active');

        $this->tenantJson('POST', $domain->host, '/login', [
            'email' => $user->email,
            'password' => 'secret-password',
        ])
            ->assertOk();

        $this->tenantJson('POST', $domain->host, '/logout', [], [], [], false)
            ->assertStatus(419);
    }

    public function test_it_rate_limits_login_attempts_per_tenant_and_email(): void
    {
        [, $domain, $user] = $this->createTenantWithUserAndMembership('active');

        for ($attempt = 1; $attempt <= 5; $attempt++) {
            $this->tenantJson('POST', $domain->host, '/login', [
                'email' => $user->email,
                'password' => 'wrong-password',
            ])
                ->assertStatus(422);
        }

        $this->tenantJson('POST', $domain->host, '/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ])
            ->assertStatus(429);
    }

    public function test_it_keeps_rate_limit_separate_between_tenants(): void
    {
        [$tenantA, $domainA, $tenantB, $domainB, $user] = $this->createSharedUserGraph();

        for ($attempt = 1; $attempt <= 5; $attempt++) {
            $this->tenantJson('POST', $domainA->host, '/login', [
                'email' => $user->email,
                'password' => 'wrong-password',
            ])
                ->assertStatus(422);
        }

        $this->tenantJson('POST', $domainA->host, '/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ])
            ->assertStatus(429);

        $this->tenantJson('POST', $domainB->host, '/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ])
            ->assertStatus(422);
    }

    public function test_it_clears_context_after_an_exception_during_tenant_resolution(): void
    {
        [, $domain] = $this->createTenantWithDomain('active');

        $request = Request::create(
            '/__tenant/context',
            'GET',
            [],
            [],
            [],
            [
                'HTTP_HOST' => $domain->host,
                'HTTP_ACCEPT' => 'application/json',
            ],
        );

        try {
            app(ResolveTenantFromHost::class)->handle(
                $request,
                static function (): Response {
                    throw new RuntimeException('Forced exception for cleanup test.');
                },
            );
        } catch (RuntimeException) {
            // expected for cleanup verification
        }

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
    private function tenantJson(
        string $method,
        string $host,
        string $uri,
        array $data = [],
        array $headers = [],
        array $cookies = [],
        bool $includeCsrf = true,
    ): TestResponse {
        $fullUri = sprintf('http://%s%s', $host, $uri);
        $server = [
            'HTTP_ACCEPT' => 'application/json',
        ];

        foreach ($headers as $name => $value) {
            $server['HTTP_'.strtoupper(str_replace('-', '_', $name))] = $value;
        }

        if ($includeCsrf) {
            $this->withoutMiddleware(ValidateCsrfToken::class);
            $data['_token'] = csrf_token();
        }

        return $this->call($method, $fullUri, $data, $cookies, [], $server);
    }

    /**
     * @param  array<string, string>  $headers
     * @param  array<string, string>  $cookies
     * @return TestResponse<Response>
     */
    private function tenantHtml(
        string $method,
        string $host,
        string $uri,
        array $data = [],
        array $headers = [],
        array $cookies = [],
        bool $includeCsrf = true,
    ): TestResponse {
        $fullUri = sprintf('http://%s%s', $host, $uri);
        $server = [
            'HTTP_ACCEPT' => 'text/html',
        ];

        foreach ($headers as $name => $value) {
            $server['HTTP_'.strtoupper(str_replace('-', '_', $name))] = $value;
        }

        if ($includeCsrf) {
            $this->withoutMiddleware(ValidateCsrfToken::class);
            $data['_token'] = csrf_token();
        }

        return $this->call($method, $fullUri, $data, $cookies, [], $server);
    }

    private function sessionCookieValue(TestResponse $response): string
    {
        $cookieName = (string) config('session.cookie');

        foreach ($response->headers->getCookies() as $cookie) {
            if ($cookie->getName() === $cookieName) {
                return $cookie->getValue();
            }
        }

        throw new RuntimeException(sprintf('Session cookie "%s" not found in response.', $cookieName));
    }

    /**
     * @return array{0: Tenant, 1: TenantDomain}
     */
    private function createTenantWithDomain(string $tenantStatus, bool $domainActive = true, string $prefix = 'tenant'): array
    {
        $tenant = Tenant::on('pgsql_migrator')->create([
            'name' => 'Tenant '.Str::uuid()->toString(),
            'slug' => $prefix.'-'.Str::uuid()->toString(),
            'status' => $tenantStatus,
        ]);

        $domain = TenantDomain::on('pgsql_migrator')->create([
            'tenant_id' => $tenant->id,
            'host' => $prefix.'-'.Str::uuid()->toString().'.example.test',
            'is_primary' => true,
            'is_active' => $domainActive,
        ]);

        return [$tenant, $domain];
    }

    /**
     * @return array{0: Tenant, 1: TenantDomain, 2: User}
     */
    private function createTenantWithUserAndMembership(
        string $tenantStatus,
        string $membershipStatus = 'active',
        string $prefix = 'tenant',
    ): array {
        [$tenant, $domain, $user] = $this->createTenantWithUserAndMembershipRecord(
            $tenantStatus,
            $membershipStatus,
            $prefix,
        );

        return [$tenant, $domain, $user];
    }

    /**
     * @return array{0: Tenant, 1: TenantDomain, 2: Tenant, 3: TenantDomain, 4: User}
     */
    private function createSharedUserGraph(): array
    {
        [$tenantA, $domainA] = $this->createTenantWithDomain('active', true, 'tenant-a');
        [$tenantB, $domainB] = $this->createTenantWithDomain('active', true, 'tenant-b');

        $user = User::on('pgsql_migrator')->create([
            'name' => 'Shared User '.Str::uuid()->toString(),
            'email' => 'shared-'.Str::uuid()->toString().'@example.test',
            'password' => 'secret-password',
        ]);

        TenantMembership::on('pgsql_migrator')->create([
            'tenant_id' => $tenantA->id,
            'user_id' => $user->id,
            'role' => 'owner',
            'status' => 'active',
        ]);

        TenantMembership::on('pgsql_migrator')->create([
            'tenant_id' => $tenantB->id,
            'user_id' => $user->id,
            'role' => 'member',
            'status' => 'active',
        ]);

        return [$tenantA, $domainA, $tenantB, $domainB, $user];
    }

    /**
     * @return array{0: Tenant, 1: TenantDomain, 2: User}
     */
    private function createTenantAndForeignUserGraph(): array
    {
        [$tenantA, $domainA] = $this->createTenantWithDomain('active', true, 'tenant-a');
        [$tenantB, $domainB] = $this->createTenantWithDomain('active', true, 'tenant-b');

        $user = User::on('pgsql_migrator')->create([
            'name' => 'Foreign User '.Str::uuid()->toString(),
            'email' => 'foreign-'.Str::uuid()->toString().'@example.test',
            'password' => 'secret-password',
        ]);

        TenantMembership::on('pgsql_migrator')->create([
            'tenant_id' => $tenantA->id,
            'user_id' => $user->id,
            'role' => 'member',
            'status' => 'active',
        ]);

        return [$tenantA, $domainA, $tenantB, $domainB, $user];
    }

    /**
     * @return array{0: Tenant, 1: TenantDomain, 2: User, 3: TenantMembership}
     */
    private function createTenantWithUserAndMembershipRecord(
        string $tenantStatus,
        string $membershipStatus = 'active',
        string $prefix = 'tenant',
    ): array {
        [$tenant, $domain] = $this->createTenantWithDomain($tenantStatus, true, $prefix);

        $user = User::on('pgsql_migrator')->create([
            'name' => 'User '.Str::uuid()->toString(),
            'email' => 'user-'.Str::uuid()->toString().'@example.test',
            'password' => 'secret-password',
        ]);

        $membership = TenantMembership::on('pgsql_migrator')->create([
            'tenant_id' => $tenant->id,
            'user_id' => $user->id,
            'role' => 'owner',
            'status' => $membershipStatus,
        ]);

        return [$tenant, $domain, $user, $membership];
    }
}

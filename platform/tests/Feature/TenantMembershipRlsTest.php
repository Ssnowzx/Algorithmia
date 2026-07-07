<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Domain\Identity\Models\User;
use App\Domain\Tenancy\CurrentTenant;
use App\Domain\Tenancy\Models\TenantDomain;
use App\Domain\Tenancy\Models\Tenant;
use App\Domain\Tenancy\Models\TenantMembership;
use App\Domain\Tenancy\Support\TenantDatabaseContext;
use Illuminate\Database\QueryException;
use Illuminate\Support\Str;
use Tests\TestCase;

final class TenantMembershipRlsTest extends TestCase
{
    public function test_a_user_can_belong_to_two_tenants(): void
    {
        [$tenantA, $tenantB, $user] = $this->createGraph();

        self::assertSame(
            2,
            TenantMembership::on('pgsql_migrator')->where('user_id', $user->id)->count(),
        );
        self::assertSame(
            [$tenantA->id, $tenantB->id],
            TenantMembership::on('pgsql_migrator')->where('user_id', $user->id)->orderBy('tenant_id')->pluck('tenant_id')->all(),
        );
    }

    public function test_reading_memberships_without_tenant_context_returns_no_rows(): void
    {
        [$tenantA, $tenantB, $user] = $this->createGraph();

        self::assertSame(0, TenantMembership::query()->count());
    }

    public function test_tenant_a_sees_only_its_memberships(): void
    {
        [$tenantA, $tenantB, $user] = $this->createGraph();
        $context = app(TenantDatabaseContext::class);

        $visibleTenantIds = $context->run(
            new CurrentTenant($tenantA->id, 'tenant-a.example.test'),
            static fn (): array => TenantMembership::query()->where('user_id', $user->id)->pluck('tenant_id')->all(),
        );

        self::assertSame([$tenantA->id], $visibleTenantIds);
    }

    public function test_tenant_a_cannot_see_memberships_from_tenant_b(): void
    {
        [$tenantA, $tenantB, $user] = $this->createGraph();
        $context = app(TenantDatabaseContext::class);

        $visibleTenantIds = $context->run(
            new CurrentTenant($tenantA->id, 'tenant-a.example.test'),
            static fn (): array => TenantMembership::query()->pluck('tenant_id')->all(),
        );

        self::assertSame([$tenantA->id], $visibleTenantIds);
    }

    public function test_tenant_a_cannot_insert_a_membership_for_tenant_b(): void
    {
        [$tenantA, $tenantB, $user] = $this->createGraph();
        $context = app(TenantDatabaseContext::class);

        $this->expectException(QueryException::class);

        $context->run(
            new CurrentTenant($tenantA->id, 'tenant-a.example.test'),
            static function () use ($tenantB, $user): void {
                TenantMembership::create([
                    'tenant_id' => $tenantB->id,
                    'user_id' => $user->id,
                    'role' => 'member',
                    'status' => 'active',
                ]);
            },
        );
    }

    public function test_tenant_a_cannot_update_or_delete_a_membership_from_tenant_b(): void
    {
        [$tenantA, $tenantB, $user] = $this->createGraph();
        $context = app(TenantDatabaseContext::class);

        $membershipB = TenantMembership::on('pgsql_migrator')->create([
            'tenant_id' => $tenantB->id,
            'user_id' => $user->id,
            'role' => 'member',
            'status' => 'active',
        ]);

        $updated = $context->run(
            new CurrentTenant($tenantA->id, 'tenant-a.example.test'),
            static fn (): int => TenantMembership::whereKey($membershipB->id)->update(['role' => 'admin']),
        );
        $deleted = $context->run(
            new CurrentTenant($tenantA->id, 'tenant-a.example.test'),
            static fn (): int => TenantMembership::whereKey($membershipB->id)->delete(),
        );

        self::assertSame(0, $updated);
        self::assertSame(0, $deleted);
        self::assertSame('member', TenantMembership::on('pgsql_migrator')->findOrFail($membershipB->id)->role);
    }

    public function test_runtime_direct_access_to_tenants_and_domains_is_denied(): void
    {
        [$tenantA, $tenantB, $user] = $this->createGraph();

        foreach ([Tenant::class, TenantDomain::class] as $modelClass) {
            try {
                $modelClass::query()->count();

                $this->fail(sprintf('%s should not be directly accessible by the runtime role.', $modelClass));
            } catch (QueryException) {
                continue;
            }
        }
    }

    /**
     * @return array{0: Tenant, 1: Tenant, 2: User}
     */
    private function createGraph(): array
    {
        $tenantA = Tenant::on('pgsql_migrator')->create([
            'name' => 'Tenant A '.Str::uuid()->toString(),
            'slug' => 'tenant-a-'.Str::uuid()->toString(),
            'status' => 'active',
        ]);

        $tenantB = Tenant::on('pgsql_migrator')->create([
            'name' => 'Tenant B '.Str::uuid()->toString(),
            'slug' => 'tenant-b-'.Str::uuid()->toString(),
            'status' => 'active',
        ]);

        $user = User::on('pgsql_migrator')->create([
            'name' => 'User '.Str::uuid()->toString(),
            'email' => 'user-'.Str::uuid()->toString().'@example.test',
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

        return [$tenantA, $tenantB, $user];
    }
}

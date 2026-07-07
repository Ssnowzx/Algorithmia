<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::connection('pgsql_migrator')->statement(<<<'SQL'
create or replace function public.resolve_active_tenant(hostname text)
returns uuid
language sql
stable
security definer
set search_path = public, pg_temp
as $$
    select td.tenant_id
    from tenant_domains td
    inner join tenants t on t.id = td.tenant_id
    where td.host = lower(hostname)
      and td.is_active = true
      and t.status = 'active'
    order by td.is_primary desc, td.created_at asc
    limit 1
$$;
SQL);

        DB::connection('pgsql_migrator')->statement('alter table tenant_memberships enable row level security');
        DB::connection('pgsql_migrator')->statement('alter table tenant_memberships force row level security');
        DB::connection('pgsql_migrator')->statement("drop policy if exists tenant_memberships_isolation on tenant_memberships");
        DB::connection('pgsql_migrator')->statement(<<<'SQL'
create policy tenant_memberships_isolation on tenant_memberships
    for all
    to algorithmia_runtime
    using (tenant_id = nullif(current_setting('app.tenant_id', true), '')::uuid)
    with check (tenant_id = nullif(current_setting('app.tenant_id', true), '')::uuid)
SQL);

        $configuredRuntimeRole = config('database.connections.pgsql.username', 'algorithmia_runtime');
        $runtimeRole = is_string($configuredRuntimeRole) && $configuredRuntimeRole !== ''
            ? $configuredRuntimeRole
            : 'algorithmia_runtime';

        DB::connection('pgsql_migrator')->statement(sprintf('grant execute on function public.resolve_active_tenant(text) to %s', $this->quoteIdentifier($runtimeRole)));
        DB::connection('pgsql_migrator')->statement(sprintf('grant select, insert, update, delete on tenant_memberships to %s', $this->quoteIdentifier($runtimeRole)));
        DB::connection('pgsql_migrator')->statement(sprintf('revoke all on table tenants, tenant_domains, users from %s', $this->quoteIdentifier($runtimeRole)));
    }

    public function down(): void
    {
        DB::connection('pgsql_migrator')->statement('drop policy if exists tenant_memberships_isolation on tenant_memberships');
        DB::connection('pgsql_migrator')->statement('alter table tenant_memberships no force row level security');
        DB::connection('pgsql_migrator')->statement('alter table tenant_memberships disable row level security');
        DB::connection('pgsql_migrator')->statement('drop function if exists public.resolve_active_tenant(text)');
    }

    private function quoteIdentifier(string $identifier): string
    {
        return '"' . str_replace('"', '""', $identifier) . '"';
    }
};

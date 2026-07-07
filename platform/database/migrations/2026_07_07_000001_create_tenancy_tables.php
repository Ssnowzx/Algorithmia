<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection('pgsql_migrator')->create('tenants', static function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('slug');
            $table->string('status', 20)->default('active');
            $table->timestampsTz();
        });

        Schema::connection('pgsql_migrator')->create('tenant_domains', static function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->restrictOnDelete();
            $table->string('host', 253);
            $table->boolean('is_primary')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestampTz('verified_at')->nullable();
            $table->timestampsTz();

            $table->index('tenant_id');
        });

        Schema::connection('pgsql_migrator')->create('users', static function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('email');
            $table->timestampTz('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestampsTz();
        });

        Schema::connection('pgsql_migrator')->create('tenant_memberships', static function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->restrictOnDelete();
            $table->foreignUuid('user_id')->constrained('users')->restrictOnDelete();
            $table->string('role', 20)->default('member');
            $table->string('status', 20)->default('active');
            $table->timestampsTz();

            $table->index('tenant_id');
            $table->index('user_id');
            $table->index('role');
            $table->index('status');
            $table->unique(['tenant_id', 'user_id']);
        });

        $this->addTenantConstraints();
        $this->addTenantDomainIndexes();
        $this->addUserConstraints();
        $this->addMembershipConstraints();
    }

    public function down(): void
    {
        Schema::connection('pgsql_migrator')->dropIfExists('tenant_memberships');
        Schema::connection('pgsql_migrator')->dropIfExists('users');
        Schema::connection('pgsql_migrator')->dropIfExists('tenant_domains');
        Schema::connection('pgsql_migrator')->dropIfExists('tenants');
    }

    private function addTenantConstraints(): void
    {
        DB::connection('pgsql_migrator')->statement("alter table tenants add constraint tenants_status_check check (status in ('active', 'suspended'))");
        DB::connection('pgsql_migrator')->statement('alter table tenants add constraint tenants_slug_lowercase_check check (slug = lower(slug))');
        DB::connection('pgsql_migrator')->statement('create unique index tenants_slug_unique on tenants (lower(slug))');
    }

    private function addTenantDomainIndexes(): void
    {
        DB::connection('pgsql_migrator')->statement('alter table tenant_domains add constraint tenant_domains_host_lowercase_check check (host = lower(host))');
        DB::connection('pgsql_migrator')->statement('create unique index tenant_domains_host_unique on tenant_domains (lower(host))');
        DB::connection('pgsql_migrator')->statement('create unique index tenant_domains_primary_active_unique on tenant_domains (tenant_id) where is_primary and is_active');
    }

    private function addUserConstraints(): void
    {
        DB::connection('pgsql_migrator')->statement('alter table users add constraint users_email_lowercase_check check (email = lower(email))');
        DB::connection('pgsql_migrator')->statement('create unique index users_email_unique on users (lower(email))');
    }

    private function addMembershipConstraints(): void
    {
        DB::connection('pgsql_migrator')->statement("alter table tenant_memberships add constraint tenant_memberships_role_check check (role in ('owner', 'admin', 'manager', 'member'))");
        DB::connection('pgsql_migrator')->statement("alter table tenant_memberships add constraint tenant_memberships_status_check check (status in ('active', 'suspended'))");
    }
};

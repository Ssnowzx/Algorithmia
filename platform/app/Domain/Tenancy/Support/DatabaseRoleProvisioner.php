<?php

declare(strict_types=1);

namespace App\Domain\Tenancy\Support;

use Illuminate\Database\ConnectionInterface;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use stdClass;
use Throwable;

final class DatabaseRoleProvisioner
{
    public function provisionRuntimeRole(): void
    {
        $connection = DB::connection('pgsql_migrator');
        $runtimeRole = $this->runtimeRoleName();
        $runtimePassword = $this->runtimeRolePassword();
        $currentRole = $this->currentRoleName($connection);

        if ($currentRole === $runtimeRole) {
            throw new RuntimeException('The migrator connection must not use the runtime role.');
        }

        $currentState = $this->currentRoleState($connection, $currentRole);

        if (! $currentState->canCreateRole) {
            throw new RuntimeException('The migrator connection cannot provision database roles.');
        }

        try {
            if ($this->roleExists($connection, $runtimeRole)) {
                $this->alterRuntimeRole($connection, $runtimeRole, $runtimePassword);
            } else {
                $this->createRuntimeRole($connection, $runtimeRole, $runtimePassword);
            }
        } catch (Throwable) {
            throw new RuntimeException('Unable to provision the runtime database role.');
        }

        $this->assertRuntimeRoleIsRestricted($connection, $runtimeRole);
    }

    private function runtimeRoleName(): string
    {
        $value = config('database.connections.pgsql.username', 'algorithmia_runtime');

        return is_string($value) && $value !== '' ? $value : 'algorithmia_runtime';
    }

    private function runtimeRolePassword(): string
    {
        $password = config('database.connections.pgsql.password', '');

        if (! is_string($password) || $password === '') {
            throw new RuntimeException('Missing runtime database password.');
        }

        return $password;
    }

    private function currentRoleName(ConnectionInterface $connection): string
    {
        $result = $connection->selectOne('select current_user as current_user');

        if (! $result instanceof stdClass || ! isset($result->current_user) || ! is_string($result->current_user)) {
            throw new RuntimeException('Unable to determine the current database role.');
        }

        return $result->current_user;
    }

    /**
     * @return object{
     *     rolsuper: bool,
     *     rolbypassrls: bool,
     *     rolcreaterole: bool,
     *     rolcreatedb: bool,
     *     canCreateRole: bool
     * }
     */
    private function currentRoleState(ConnectionInterface $connection, string $roleName): object
    {
        $result = $connection->selectOne(
            'select rolsuper, rolbypassrls, rolcreaterole, rolcreatedb from pg_roles where rolname = ?',
            [$roleName],
        );

        if (! $result instanceof stdClass || ! isset($result->rolsuper, $result->rolbypassrls, $result->rolcreaterole, $result->rolcreatedb)) {
            throw new RuntimeException('Unable to inspect migrator role privileges.');
        }

        return (object) [
            'rolsuper' => (bool) $result->rolsuper,
            'rolbypassrls' => (bool) $result->rolbypassrls,
            'rolcreaterole' => (bool) $result->rolcreaterole,
            'rolcreatedb' => (bool) $result->rolcreatedb,
            'canCreateRole' => (bool) $result->rolcreaterole || (bool) $result->rolsuper,
        ];
    }

    private function roleExists(ConnectionInterface $connection, string $roleName): bool
    {
        $result = $connection->selectOne(
            'select 1 as role_exists from pg_roles where rolname = ?',
            [$roleName],
        );

        return $result !== null;
    }

    private function createRuntimeRole(ConnectionInterface $connection, string $roleName, string $password): void
    {
        $connection->statement(
            sprintf(
                'create role %s with login nosuperuser nocreatedb nocreaterole noinherit nobypassrls password %s',
                $this->quoteIdentifier($roleName),
                $this->quoteLiteral($password),
            ),
        );
    }

    private function alterRuntimeRole(ConnectionInterface $connection, string $roleName, string $password): void
    {
        $connection->statement(
            sprintf(
                'alter role %s with login nosuperuser nocreatedb nocreaterole noinherit nobypassrls password %s',
                $this->quoteIdentifier($roleName),
                $this->quoteLiteral($password),
            ),
        );
    }

    private function assertRuntimeRoleIsRestricted(ConnectionInterface $connection, string $roleName): void
    {
        $result = $connection->selectOne(
            'select rolsuper, rolbypassrls, rolcreaterole, rolcreatedb from pg_roles where rolname = ?',
            [$roleName],
        );

        if (! $result instanceof stdClass || ! isset($result->rolsuper, $result->rolbypassrls, $result->rolcreaterole, $result->rolcreatedb)) {
            throw new RuntimeException('Unable to inspect runtime role restrictions.');
        }

        if ((bool) $result->rolsuper || (bool) $result->rolbypassrls || (bool) $result->rolcreaterole || (bool) $result->rolcreatedb) {
            throw new RuntimeException('Runtime role has unexpected database privileges.');
        }
    }

    private function quoteIdentifier(string $identifier): string
    {
        return '"'.str_replace('"', '""', $identifier).'"';
    }

    private function quoteLiteral(string $value): string
    {
        return "'".str_replace("'", "''", $value)."'";
    }
}

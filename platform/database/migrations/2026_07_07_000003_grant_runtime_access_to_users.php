<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $runtimeRole = $this->runtimeRoleName();

        DB::connection('pgsql_migrator')->statement(sprintf(
            'grant select on table users to %s',
            $this->quoteIdentifier($runtimeRole),
        ));
    }

    public function down(): void
    {
        $runtimeRole = $this->runtimeRoleName();

        DB::connection('pgsql_migrator')->statement(sprintf(
            'revoke select on table users from %s',
            $this->quoteIdentifier($runtimeRole),
        ));
    }

    private function runtimeRoleName(): string
    {
        $value = config('database.connections.pgsql.username', 'algorithmia_runtime');

        return is_string($value) && $value !== '' ? $value : 'algorithmia_runtime';
    }

    private function quoteIdentifier(string $identifier): string
    {
        return '"'.str_replace('"', '""', $identifier).'"';
    }
};

<?php

declare(strict_types=1);

use App\Domain\Tenancy\Support\DatabaseRoleProvisioner;
use Illuminate\Support\Facades\Artisan;

Artisan::command('platform:database:provision-runtime-role', function (): int {
    app(DatabaseRoleProvisioner::class)->provisionRuntimeRole();

    return 0;
})->purpose('Provision the runtime PostgreSQL role for the Algorithmia platform');

<?php

declare(strict_types=1);

namespace App\Domain\Tenancy;

use Illuminate\Support\Str;
use InvalidArgumentException;

final readonly class CurrentTenant
{
    public function __construct(
        public string $tenantId,
        public string $host,
    ) {
        if (! Str::isUuid($tenantId)) {
            throw new InvalidArgumentException('Current tenant id must be a valid UUID.');
        }

        if ($host === '') {
            throw new InvalidArgumentException('Current tenant host cannot be empty.');
        }
    }
}

<?php

declare(strict_types=1);

namespace App\Domain\Tenancy\Support;

use InvalidArgumentException;

final class HostNormalizer
{
    public function normalize(string $host): string
    {
        if ($host !== trim($host)) {
            throw new InvalidArgumentException('Host must not contain leading or trailing whitespace.');
        }

        $candidate = strtolower($host);

        if ($candidate === '') {
            throw new InvalidArgumentException('Host cannot be empty.');
        }

        if (
            str_contains($candidate, '://')
            || str_contains($candidate, '/')
            || str_contains($candidate, '?')
            || str_contains($candidate, '#')
            || str_contains($candidate, ':')
            || str_contains($candidate, '@')
        ) {
            throw new InvalidArgumentException('Host must be a simple hostname.');
        }

        if (str_ends_with($candidate, '.')) {
            $candidate = substr($candidate, 0, -1);

            if (str_ends_with($candidate, '.')) {
                throw new InvalidArgumentException('Host cannot contain repeated trailing dots.');
            }
        }

        if (strlen($candidate) === 0 || strlen($candidate) > 253) {
            throw new InvalidArgumentException('Host length is invalid.');
        }

        if (filter_var($candidate, FILTER_VALIDATE_IP) !== false) {
            throw new InvalidArgumentException('IP addresses are not accepted as tenant hosts.');
        }

        if (! preg_match('/\A(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?)(?:\.(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?))*\z/', $candidate)) {
            throw new InvalidArgumentException('Host is malformed.');
        }

        return $candidate;
    }
}

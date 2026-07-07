<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Domain\Tenancy\Support\HostNormalizer;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class HostNormalizerTest extends TestCase
{
    public function test_it_normalizes_a_valid_hostname_and_removes_one_trailing_dot(): void
    {
        $normalizer = new HostNormalizer;

        self::assertSame('tenant.example.test', $normalizer->normalize('Tenant.Example.Test.'));
    }

    public function test_it_removes_a_development_port_from_the_host(): void
    {
        $normalizer = new HostNormalizer;

        self::assertSame('tenant.example.test', $normalizer->normalize('Tenant.Example.Test:8080'));
    }

    #[DataProvider('invalidHosts')]
    public function test_it_rejects_invalid_hosts(string $host): void
    {
        $normalizer = new HostNormalizer;

        $this->expectException(InvalidArgumentException::class);

        $normalizer->normalize($host);
    }

    /**
     * @return iterable<string, array{0: string}>
     */
    public static function invalidHosts(): iterable
    {
        yield 'protocol' => ['https://tenant.example.test'];
        yield 'path' => ['tenant.example.test/healthz'];
        yield 'query' => ['tenant.example.test?foo=bar'];
        yield 'fragment' => ['tenant.example.test#section'];
        yield 'invalid ip' => ['999.999.999.999'];
        yield 'unicode' => ["t\u{00EA}nant.example.test"];
        yield 'trailing whitespace' => ['tenant.example.test '];
    }
}

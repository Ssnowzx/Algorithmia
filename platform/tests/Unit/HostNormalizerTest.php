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
        $normalizer = new HostNormalizer();

        self::assertSame('tenant.example.test', $normalizer->normalize('Tenant.Example.Test.'));
    }

    #[DataProvider('invalidHosts')]
    public function test_it_rejects_invalid_hosts(string $host): void
    {
        $normalizer = new HostNormalizer();

        $this->expectException(InvalidArgumentException::class);

        $normalizer->normalize($host);
    }

    /**
     * @return iterable<string, array{0: string}>
     */
    public static function invalidHosts(): iterable
    {
        yield 'protocol' => ['https://tenant.example.test'];
        yield 'port' => ['tenant.example.test:8080'];
        yield 'path' => ['tenant.example.test/healthz'];
        yield 'query' => ['tenant.example.test?foo=bar'];
        yield 'fragment' => ['tenant.example.test#section'];
        yield 'ip address' => ['127.0.0.1'];
        yield 'unicode' => ['tênant.example.test'];
        yield 'trailing whitespace' => ['tenant.example.test '];
    }
}

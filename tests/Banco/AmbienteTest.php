<?php

declare(strict_types=1);

namespace Algorithmia\Testes\Banco;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Uma falha de conexão com o banco não pode imprimir host e usuário na tela do
 * jogador. O vhost do jogo define `DB_*` e não define `APP_ENV`; com o antigo
 * `(getenv('APP_ENV') ?: 'dev') === 'dev'`, produção se declarava desenvolvimento.
 */
final class AmbienteTest extends TestCase
{
    private string|false $original;

    protected function setUp(): void
    {
        $this->original = getenv('APP_ENV');
    }

    protected function tearDown(): void
    {
        if ($this->original === false) {
            putenv('APP_ENV');
        } else {
            putenv('APP_ENV=' . $this->original);
        }
    }

    public function testSemAppEnvOAmbienteEhProducao(): void
    {
        // ARRANGE: é exatamente o que o vhost de produção oferece — nada.
        putenv('APP_ENV');

        // ACT + ASSERT
        $this->assertFalse(ehAmbienteDeDesenvolvimento());
    }

    public function testProductionNaoEhDesenvolvimento(): void
    {
        // ARRANGE
        putenv('APP_ENV=production');

        // ACT + ASSERT
        $this->assertFalse(ehAmbienteDeDesenvolvimento());
    }

    /** @return list<array{string}> */
    public static function ambientesDeDesenvolvimento(): array
    {
        return [['dev'], ['local'], ['test']];
    }

    #[DataProvider('ambientesDeDesenvolvimento')]
    public function testOsAmbientesDeDesenvolvimentoMostramODetalhe(string $ambiente): void
    {
        // ARRANGE
        putenv('APP_ENV=' . $ambiente);

        // ACT + ASSERT
        $this->assertTrue(ehAmbienteDeDesenvolvimento());
    }
}

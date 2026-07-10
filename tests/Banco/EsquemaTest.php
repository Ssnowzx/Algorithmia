<?php

declare(strict_types=1);

namespace Algorithmia\Testes\Banco;

use PHPUnit\Framework\TestCase;

/**
 * O migrador precisa obedecer a `DB_NAME`.
 *
 * Até 2026-07-09, `schema.sql` trazia `CREATE DATABASE algorithmia; USE algorithmia;`
 * escritos à mão. Rodar `DB_NAME=outro php database/migrate.php` criava as tabelas em
 * `algorithmia`, aplicava as migrations no banco vazio, e falhava com "Table doesn't
 * exist" — uma mensagem que não diz nada sobre a causa.
 */
final class EsquemaTest extends TestCase
{
    private const RAIZ = __DIR__ . '/../..';

    public function testOSchemaNaoFixaONomeDoBanco(): void
    {
        // ARRANGE + ACT
        $sql = (string) file_get_contents(self::RAIZ . '/database/schema.sql');

        // ASSERT
        $this->assertDoesNotMatchRegularExpression('/^\s*CREATE\s+DATABASE/mi', $sql);
        $this->assertDoesNotMatchRegularExpression('/^\s*USE\s+/mi', $sql);
    }

    public function testAsSeedsNaoFixamONomeDoBanco(): void
    {
        // ARRANGE + ACT
        $sql = (string) file_get_contents(self::RAIZ . '/database/seeds.sql');

        // ASSERT
        $this->assertDoesNotMatchRegularExpression('/^\s*USE\s+/mi', $sql);
    }

    /**
     * Um nome de banco vira identificador SQL, e identificador não aceita placeholder.
     * A crase não escapa crase: sem a guarda, um `DB_NAME` hostil injetaria DDL.
     */
    public function testOMigradorRecusaUmNomeDeBancoInvalido(): void
    {
        // ARRANGE: um nome que fecha a crase e emenda outro comando.
        $nomeHostil = 'algo`; DROP DATABASE `x';

        // ACT: subprocesso, para não derrubar a suíte com o `exit(1)`.
        $processo = proc_open(
            [PHP_BINARY, self::RAIZ . '/database/migrate.php'],
            [1 => ['pipe', 'w'], 2 => ['pipe', 'w']],
            $canos,
            null,
            ['DB_NAME' => $nomeHostil, 'DB_USER' => 'ninguem', 'DB_PASS' => '', 'PATH' => getenv('PATH')]
        );
        $this->assertIsResource($processo);

        $erro = (string) stream_get_contents($canos[2]);
        fclose($canos[1]);
        fclose($canos[2]);
        $codigo = proc_close($processo);

        // ASSERT: recusa antes de tocar no MySQL.
        $this->assertSame(1, $codigo);
        $this->assertStringContainsString('DB_NAME inválido', $erro);
    }
}

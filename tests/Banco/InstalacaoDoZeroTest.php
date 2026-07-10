<?php

declare(strict_types=1);

namespace Algorithmia\Testes\Banco;

use PDO;
use PHPUnit\Framework\TestCase;

/**
 * Instalar o jogo do zero era impossível, e ninguém via.
 *
 * As migrations rodavam ANTES do `seeds.sql`. A `20260624-objetivos-loja.sql` insere
 * conquistas com `INSERT IGNORE` — comentário no arquivo: "para conviver com a seed".
 * Rodando primeiro, ela inseria `primeira_arma`, e o `INSERT` comum do `seeds.sql`
 * morria em `Duplicate entry`. A CI não pegava porque montava o banco de teste
 * aplicando só o `schema.sql`, sem nunca chamar o migrador.
 *
 * Este teste chama o migrador de verdade, num banco descartável.
 */
final class InstalacaoDoZeroTest extends TestCase
{
    /** O bootstrap exige `_test`; o banco deste teste também, e ele é descartável. */
    private const BANCO = 'algorithmia_zero_test';

    private function servidor(): PDO
    {
        return new PDO(
            sprintf('mysql:host=%s;charset=utf8mb4', DB_HOST),
            DB_USER,
            DB_PASS,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
    }

    protected function setUp(): void
    {
        $this->servidor()->exec('DROP DATABASE IF EXISTS ' . self::BANCO);
    }

    protected function tearDown(): void
    {
        $this->servidor()->exec('DROP DATABASE IF EXISTS ' . self::BANCO);
    }

    /** @return array{codigo:int,saida:string} */
    private function rodarMigrador(): array
    {
        $processo = proc_open(
            [PHP_BINARY, __DIR__ . '/../../database/migrate.php'],
            [1 => ['pipe', 'w'], 2 => ['pipe', 'w']],
            $canos,
            null,
            [
                'DB_HOST' => DB_HOST,
                'DB_NAME' => self::BANCO,
                'DB_USER' => DB_USER,
                'DB_PASS' => DB_PASS,
                'DEMO_SENHA' => 'senha-do-teste',
                'PATH' => getenv('PATH'),
            ]
        );

        $saida = (string) stream_get_contents($canos[1]) . (string) stream_get_contents($canos[2]);
        fclose($canos[1]);
        fclose($canos[2]);

        return ['codigo' => proc_close($processo), 'saida' => $saida];
    }

    public function testInstalarDoZeroCriaOBancoInteiro(): void
    {
        // ARRANGE: o banco nem existe. O migrador tem de criá-lo, a partir de DB_NAME.

        // ACT
        $resultado = $this->rodarMigrador();

        // ASSERT: sem `Duplicate entry`, sem `Table doesn't exist`.
        $this->assertSame(0, $resultado['codigo'], $resultado['saida']);
        $this->assertStringNotContainsString('Erro', $resultado['saida']);

        $db = $this->servidor();
        $db->exec('USE ' . self::BANCO);

        $contar = static fn (string $t): int => (int) $db->query("SELECT COUNT(*) FROM {$t}")->fetchColumn();

        $this->assertSame(5, $contar('mestres'));
        $this->assertSame(35, $contar('fases'));
        $this->assertGreaterThan(900, $contar('desafios'));
        $this->assertGreaterThan(0, $contar('conquistas'));
        $this->assertSame(6, $contar('migracoes_aplicadas'), 'as 6 migrations do legado');
    }

    public function testRodarDeNovoNaoQuebraNemApaga(): void
    {
        // ARRANGE
        $this->assertSame(0, $this->rodarMigrador()['codigo']);

        // ACT: é o que todo deploy faz.
        $segunda = $this->rodarMigrador();

        // ASSERT
        $this->assertSame(0, $segunda['codigo'], $segunda['saida']);
        $this->assertStringContainsString('Conteúdo já existe', $segunda['saida']);
        $this->assertStringContainsString('preservada', $segunda['saida']);

        $db = $this->servidor();
        $db->exec('USE ' . self::BANCO);
        $this->assertSame(35, (int) $db->query('SELECT COUNT(*) FROM fases')->fetchColumn());
    }
}

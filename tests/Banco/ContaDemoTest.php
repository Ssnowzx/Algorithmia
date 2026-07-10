<?php

declare(strict_types=1);

namespace Algorithmia\Testes\Banco;

use Algorithmia\Testes\Suporte\Mundo;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../database/seed-conta-demo.php';

/**
 * O `migrate.php` roda a cada deploy, e chama este seeder.
 *
 * Até 2026-07-09 ele carregava o arquivo incondicionalmente, e o arquivo, ao ser
 * carregado, reescrevia a senha da conta de administrador e apagava seu progresso,
 * inventário, conquistas e escolhas. Em produção, todo deploy destruía a conta e a
 * devolvia com a senha padrão publicada no repositório.
 *
 * Estes testes existem para que isso não volte por descuido.
 */
final class ContaDemoTest extends TestCase
{
    private Mundo $mundo;

    private \PDO $db;

    protected function setUp(): void
    {
        $this->mundo = new Mundo();
        $this->mundo->limpar();
        $this->db = getConnection();

        // O seeder recusa banco sem conteúdo.
        $this->mundo->fase();
    }

    /** @return array{id:int,personagem_id:int,senha_hash:string} */
    private function contaDemo(): array
    {
        $stmt = $this->db->prepare('SELECT id, senha_hash FROM usuarios WHERE email = ?');
        $stmt->execute([DEMO_EMAIL]);
        $usuario = $stmt->fetch();

        $stmt = $this->db->prepare('SELECT id FROM personagens WHERE usuario_id = ?');
        $stmt->execute([$usuario['id']]);

        return [
            'id' => (int) $usuario['id'],
            'personagem_id' => (int) $stmt->fetchColumn(),
            'senha_hash' => (string) $usuario['senha_hash'],
        ];
    }

    /** Dá progresso ao personagem, como faria um jogador de verdade. */
    private function darProgresso(int $personagemId, int $faseId): void
    {
        $this->db->prepare(
            'UPDATE personagens SET nivel = 9, xp = 4200, ouro = 999, reputacao = 7 WHERE id = ?'
        )->execute([$personagemId]);

        $this->db->prepare(
            'INSERT INTO progresso_fases (personagem_id, fase_id, estrelas, acertos, erros, usou_ia)
             VALUES (?, ?, 3, 5, 0, 0)'
        )->execute([$personagemId, $faseId]);
    }

    public function testDeveCriarAContaQuandoElaNaoExiste(): void
    {
        // ARRANGE: banco limpo, sem a conta.

        // ACT
        $relatorio = semearContaDemo($this->db, false);

        // ASSERT
        $this->assertStringContainsString('criada', $relatorio);
        $this->assertSame('mestre', $this->db->query(
            'SELECT papel FROM usuarios WHERE email = ' . $this->db->quote(DEMO_EMAIL)
        )->fetchColumn());
    }

    public function testNaoDeveTocarNumaContaQueJaExiste(): void
    {
        // ARRANGE: a conta existe e o jogador progrediu.
        semearContaDemo($this->db, false);
        $antes = $this->contaDemo();
        $faseId = (int) $this->db->query('SELECT id FROM fases LIMIT 1')->fetchColumn();
        $this->darProgresso($antes['personagem_id'], $faseId);

        // ACT: é exatamente o que o `migrate.php` faz a cada deploy.
        $relatorio = semearContaDemo($this->db, false);

        // ASSERT: nada foi tocado.
        $this->assertStringContainsString('preservada', $relatorio);

        $depois = $this->contaDemo();
        $this->assertSame($antes['senha_hash'], $depois['senha_hash'], 'a senha não pode ser reescrita');

        $pers = $this->db->query(
            'SELECT nivel, xp, ouro, reputacao FROM personagens WHERE id = ' . $antes['personagem_id']
        )->fetch();
        $this->assertSame(9, (int) $pers['nivel']);
        $this->assertSame(4200, (int) $pers['xp']);
        $this->assertSame(999, (int) $pers['ouro']);
        $this->assertSame(7, (int) $pers['reputacao']);

        $this->assertSame(1, (int) $this->db->query(
            'SELECT COUNT(*) FROM progresso_fases WHERE personagem_id = ' . $antes['personagem_id']
        )->fetchColumn(), 'o progresso não pode ser apagado');
    }

    public function testForcarZeraAContaDePropositoPoisEOQueElePromete(): void
    {
        // ARRANGE
        semearContaDemo($this->db, false);
        $conta = $this->contaDemo();
        $faseId = (int) $this->db->query('SELECT id FROM fases LIMIT 1')->fetchColumn();
        $this->darProgresso($conta['personagem_id'], $faseId);

        // ACT
        $relatorio = semearContaDemo($this->db, true);

        // ASSERT
        $this->assertStringContainsString('recriada', $relatorio);

        $pers = $this->db->query(
            'SELECT nivel, ouro FROM personagens WHERE id = ' . $conta['personagem_id']
        )->fetch();
        $this->assertSame(1, (int) $pers['nivel']);
        $this->assertSame(50, (int) $pers['ouro']);
        $this->assertSame(0, (int) $this->db->query(
            'SELECT COUNT(*) FROM progresso_fases WHERE personagem_id = ' . $conta['personagem_id']
        )->fetchColumn());
    }

    public function testSemDemoSenhaNoAmbienteASenhaEhSorteadaENaoAPublicaDoRepositorio(): void
    {
        // ARRANGE: garantir que o ambiente não define a senha.
        putenv('DEMO_SENHA');

        // ACT
        [$senha, $sorteada] = senhaDaContaDemo();

        // ASSERT: uma instalação nova não pode nascer com senha conhecida.
        $this->assertTrue($sorteada);
        $this->assertNotSame('qwe123', $senha);
        $this->assertSame(18, strlen($senha));
    }

    public function testDemoSenhaDoAmbienteEhRespeitada(): void
    {
        // ARRANGE
        putenv('DEMO_SENHA=escolhida-pelo-operador');

        try {
            // ACT
            [$senha, $sorteada] = senhaDaContaDemo();

            // ASSERT
            $this->assertFalse($sorteada);
            $this->assertSame('escolhida-pelo-operador', $senha);
        } finally {
            putenv('DEMO_SENHA');
        }
    }
}

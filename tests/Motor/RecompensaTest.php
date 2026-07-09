<?php

declare(strict_types=1);

namespace Algorithmia\Testes\Motor;

use Algorithmia\Testes\Suporte\Mundo;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Vetores-ouro da concessão de recompensas (XP, ouro, estrelas, reputação,
 * progresso e conquistas). É a parte do motor cuja duplicação corrompe a conta
 * do jogador de forma silenciosa e difícil de reverter.
 *
 * Curva de XP: xpParaNivel(N) = round(100 * (N-1)^1.5) → nível 2 exige 100.
 */
final class RecompensaTest extends TestCase
{
    private Mundo $mundo;

    protected function setUp(): void
    {
        $this->mundo = new Mundo();
        $this->mundo->limpar();
    }

    #[Test]
    public function deve_conceder_xp_ouro_estrelas_e_reputacao_numa_vitoria_limpa(): void
    {
        // ARRANGE
        $this->mundo->conquistas('primeiro_passo', 'sem_falhas');
        $heroi = $this->mundo->heroi('mago', ['ouro' => 50]);
        $fase = $this->mundo->fase(['xp_recompensa' => 50, 'ouro_recompensa' => 20]);

        // ACT
        $r = (new \RecompensaService())->conceder($heroi, $this->estadoVitorioso($fase, erros: 0, usouIa: false));

        // ASSERT
        $this->assertSame(3, $r['estrelas'], 'sem erros e sem IA → 3 estrelas');
        $this->assertSame(50, $r['xp']);
        $this->assertSame(20, $r['ouro']);
        $this->assertSame(0, $r['niveis'], '50 XP não alcança os 100 do nível 2');

        $depois = $this->mundo->recarregarHeroi((int) $heroi['id']);
        $this->assertSame(70, (int) $depois['ouro']);
        $this->assertSame(50, (int) $depois['xp']);
        $this->assertSame(5, (int) $depois['reputacao'], 'vencer sem IA sobe a disciplina');

        $codigos = array_column($r['conquistas'], 'nome');
        $this->assertContains('Primeiro passo', $codigos);
        $this->assertContains('Sem falhas', $codigos);
    }

    #[Test]
    public function o_ranger_leva_20_por_cento_a_mais_de_ouro(): void
    {
        // ARRANGE
        $heroi = $this->mundo->heroi('ranger', ['ouro' => 0]);
        $fase = $this->mundo->fase(['ouro_recompensa' => 20]);

        // ACT
        $r = (new \RecompensaService())->conceder($heroi, $this->estadoVitorioso($fase));

        // ASSERT
        $this->assertSame(24, $r['ouro'], 'round(20 * 1.2)');
        $this->assertSame(24, (int) $this->mundo->recarregarHeroi((int) $heroi['id'])['ouro']);
    }

    #[Test]
    public function subir_de_nivel_amplia_e_restaura_vida_e_mana(): void
    {
        // ARRANGE: mago machucado, a um XP de fechar o nível 2 (exige 100).
        $heroi = $this->mundo->heroi('mago', ['xp' => 0, 'hp_atual' => 10, 'mp_atual' => 3]);
        $fase = $this->mundo->fase(['xp_recompensa' => 100]);

        // ACT
        $r = (new \RecompensaService())->conceder($heroi, $this->estadoVitorioso($fase));

        // ASSERT
        $this->assertSame(1, $r['niveis']);
        $this->assertSame(2, $r['nivel']);

        $depois = $this->mundo->recarregarHeroi((int) $heroi['id']);
        $this->assertSame(95, (int) $depois['hp_max'], '80 + 15');
        $this->assertSame(68, (int) $depois['mp_max'], '60 + 8');
        $this->assertSame(95, (int) $depois['hp_atual'], 'subir de nível cura por completo');
        $this->assertSame(68, (int) $depois['mp_atual']);
    }

    /** @return array<string,array{0:int,1:bool,2:int}> [erros, usouIa, estrelas] */
    public static function cenariosDeEstrelas(): array
    {
        return [
            'perfeito' => [0, false, 3],
            'dois erros' => [2, false, 2],
            'tres erros' => [3, false, 1],
            'usou IA anula o mérito' => [0, true, 1],
        ];
    }

    #[Test]
    #[DataProvider('cenariosDeEstrelas')]
    public function deve_calcular_estrelas_por_erros_e_uso_de_ia(int $erros, bool $usouIa, int $estrelas): void
    {
        // ARRANGE + ACT + ASSERT
        $this->assertSame($estrelas, (new \ProgressaoService())->calcularEstrelas($erros, $usouIa));
    }

    #[Test]
    public function vencer_usando_a_ia_nao_sobe_a_reputacao(): void
    {
        // ARRANGE
        $heroi = $this->mundo->heroi('mago', ['reputacao' => -10]);
        $fase = $this->mundo->fase();

        // ACT
        $r = (new \RecompensaService())->conceder($heroi, $this->estadoVitorioso($fase, erros: 0, usouIa: true));

        // ASSERT
        $this->assertSame(1, $r['estrelas']);
        $this->assertSame(-10, (int) $this->mundo->recarregarHeroi((int) $heroi['id'])['reputacao']);
    }

    #[Test]
    public function o_progresso_da_fase_guarda_estrelas_acertos_e_a_marca_da_ia(): void
    {
        // ARRANGE
        $heroi = $this->mundo->heroi('mago');
        $fase = $this->mundo->fase();

        // ACT
        (new \RecompensaService())->conceder($heroi, $this->estadoVitorioso($fase, erros: 1, usouIa: false, acertos: 3));

        // ASSERT
        $mapa = (new \ProgressoFase())->mapaDoPersonagem((int) $heroi['id']);
        $linha = $mapa[(int) $fase['id']];
        $this->assertSame(2, (int) $linha['estrelas'], 'um erro → 2 estrelas');
        $this->assertSame(3, (int) $linha['acertos']);
        $this->assertSame(1, (int) $linha['erros']);
        $this->assertSame(0, (int) $linha['usou_ia']);
    }

    #[Test]
    public function o_drop_da_fase_entra_no_inventario_uma_unica_vez(): void
    {
        // ARRANGE
        $heroi = $this->mundo->heroi('mago');
        $espada = $this->mundo->item('arma', ['ataque' => 5]);
        $fase = $this->mundo->fase(['item_drop_id' => (int) $espada['id']]);

        // ACT: vence a mesma fase duas vezes (replay é permitido pelo jogo).
        $primeira = (new \RecompensaService())->conceder($heroi, $this->estadoVitorioso($fase));
        $segunda = (new \RecompensaService())->conceder(
            $this->mundo->recarregarHeroi((int) $heroi['id']),
            $this->estadoVitorioso($fase)
        );

        // ASSERT
        $this->assertNotNull($primeira['item_drop']);
        $this->assertNull($segunda['item_drop'], 'quem já tem o item não recebe de novo');
        $this->assertSame(1, (new \Inventario())->quantidade((int) $heroi['id'], (int) $espada['id']));
    }

    #[Test]
    public function conceder_duas_vezes_duplica_a_reputacao_a_guarda_vive_na_sessao(): void
    {
        // Caracterização de um risco real do port: RecompensaService NÃO é
        // idempotente. Hoje a proteção contra duplo-crédito é o flag
        // 'recompensado' de $_SESSION['batalha'], checado por
        // BatalhaController::finalizarSePreciso — não há chave no banco.
        //
        // XP e ouro escapam por acidente: ambos são gravados a partir da MESMA
        // linha $heroi lida antes da primeira chamada (valor absoluto, não
        // incremento). Já a reputação relê o personagem do banco e soma — então
        // duplica. Ao portar, isto vira uma chave de idempotência de verdade.

        // ARRANGE
        $heroi = $this->mundo->heroi('mago', ['ouro' => 50, 'xp' => 0, 'reputacao' => 0]);
        $fase = $this->mundo->fase(['xp_recompensa' => 50, 'ouro_recompensa' => 20]);
        $estado = $this->estadoVitorioso($fase);

        // ACT: a mesma concessão, com a mesma linha de herói, duas vezes.
        (new \RecompensaService())->conceder($heroi, $estado);
        (new \RecompensaService())->conceder($heroi, $estado);

        // ASSERT
        $depois = $this->mundo->recarregarHeroi((int) $heroi['id']);
        $this->assertSame(70, (int) $depois['ouro'], 'ouro é escrito como valor absoluto');
        $this->assertSame(50, (int) $depois['xp'], 'XP também');
        $this->assertSame(10, (int) $depois['reputacao'], 'reputação SOMA sobre o banco → duplicou');
    }

    #[Test]
    public function derrotar_o_chefe_de_uma_regiao_avanca_o_capitulo_e_da_a_conquista(): void
    {
        // ARRANGE: o código da conquista vem de REGIOES_MESTRE, chaveado por svg_slug.
        $this->mundo->conquistas('mestre_willen', 'cacador_de_chefes', 'primeiro_passo', 'puro_de_coracao');
        $mestre = $this->mundo->mestre(['svg_slug' => 'mestre-willen', 'ordem' => 1]);
        $heroi = $this->mundo->heroi('mago', ['capitulo' => 0]);
        $fase = $this->mundo->fase(['tipo' => 'chefe', 'mestre_id' => (int) $mestre['id']]);

        // ACT
        $r = (new \RecompensaService())->conceder($heroi, $this->estadoVitorioso($fase));

        // ASSERT
        $nomes = array_column($r['conquistas'], 'nome');
        $this->assertContains('Mestre willen', $nomes, 'discípulo da região');
        $this->assertContains('Cacador de chefes', $nomes);
        $this->assertContains('Puro de coracao', $nomes, 'região inteira concluída sem IA');
        $this->assertSame(1, (int) $this->mundo->recarregarHeroi((int) $heroi['id'])['capitulo']);
    }

    #[Test]
    public function usar_a_ia_em_qualquer_fase_da_regiao_bloqueia_puro_de_coracao(): void
    {
        // ARRANGE: uma lição já concluída COM IA, depois o chefe vencido sem IA.
        $this->mundo->conquistas('mestre_willen', 'cacador_de_chefes', 'primeiro_passo', 'puro_de_coracao');
        $mestre = $this->mundo->mestre(['svg_slug' => 'mestre-willen', 'ordem' => 1]);
        $heroi = $this->mundo->heroi('mago');
        $licao = $this->mundo->fase(['tipo' => 'licao', 'mestre_id' => (int) $mestre['id'], 'ordem_global' => 1]);
        $chefe = $this->mundo->fase(['tipo' => 'chefe', 'mestre_id' => (int) $mestre['id'], 'ordem_global' => 2]);

        (new \ProgressoFase())->registrar((int) $heroi['id'], (int) $licao['id'], 1, 2, 0, true);

        // ACT
        $r = (new \RecompensaService())->conceder($heroi, $this->estadoVitorioso($chefe));

        // ASSERT
        $this->assertNotContains('Puro de coracao', array_column($r['conquistas'], 'nome'));
    }

    /**
     * Estado de batalha mínimo que RecompensaService consome.
     *
     * @return array<string,mixed>
     */
    private function estadoVitorioso(array $fase, int $erros = 0, bool $usouIa = false, int $acertos = 4): array
    {
        return [
            'fase_id' => (int) $fase['id'],
            'erros' => $erros,
            'usou_ia' => $usouIa,
            'acertos' => $acertos,
        ];
    }
}

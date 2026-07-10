<?php

declare(strict_types=1);

namespace Tests\Dominio;

use App\Dominio\Progressao\ResultadoDaBatalha;
use App\Dominio\Progressao\ServicoDeConquistas;
use App\Dominio\Progressao\ServicoDeProgressao;
use App\Dominio\Progressao\ServicoDeRecompensa;
use App\Dominio\Progressao\ServicoDeReputacao;
use App\Models\Fase;
use App\Models\ItemDoInventario;
use App\Models\ProgressoFase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\RefreshDatabase;
use Tests\Suporte\Mundo;
use Tests\TestCase;

/**
 * Vetores-ouro da concessão de recompensas, portados de
 * `tests/Motor/RecompensaTest.php` do legado.
 *
 * Uma divergência deliberada em relação ao legado: a concessão agora é
 * **idempotente**. Onde o legado duplicava a reputação ao ser chamado duas
 * vezes, aqui a segunda chamada é um no-op. Está no fim deste arquivo, e é a
 * dívida que a Fase 2 do PLANO.md se propôs a pagar.
 *
 * Curva de XP: xpParaNivel(N) = round(100 * (N-1)^1.5) → o nível 2 exige 100.
 */
final class RecompensaTest extends TestCase
{
    use RefreshDatabase;

    private Mundo $mundo;

    private ServicoDeRecompensa $recompensa;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mundo = new Mundo;
        $this->recompensa = new ServicoDeRecompensa(
            new ServicoDeProgressao,
            new ServicoDeReputacao,
            new ServicoDeConquistas,
        );
    }

    #[Test]
    public function deve_conceder_xp_ouro_estrelas_e_reputacao_numa_vitoria_limpa(): void
    {
        // ARRANGE
        $this->mundo->conquistas('primeiro_passo', 'sem_falhas');
        $heroi = $this->mundo->heroi('mago', ['ouro' => 50]);
        $fase = $this->mundo->fase(['xp_recompensa' => 50, 'ouro_recompensa' => 20]);

        // ACT
        $r = $this->recompensa->conceder($heroi, $this->desfecho($fase));

        // ASSERT
        $this->assertNotNull($r);
        $this->assertSame(3, $r['estrelas'], 'sem erros e sem IA → 3 estrelas');
        $this->assertSame(50, $r['xp']);
        $this->assertSame(20, $r['ouro']);
        $this->assertSame(0, $r['niveis'], '50 XP não alcança os 100 do nível 2');

        $heroi->refresh();
        $this->assertSame(70, $heroi->ouro);
        $this->assertSame(50, $heroi->xp);
        $this->assertSame(5, $heroi->reputacao, 'vencer sem IA sobe a disciplina');

        $nomes = array_column($r['conquistas'], 'nome');
        $this->assertContains('Primeiro passo', $nomes);
        $this->assertContains('Sem falhas', $nomes);
    }

    #[Test]
    public function o_ranger_leva_20_por_cento_a_mais_de_ouro(): void
    {
        // ARRANGE
        $heroi = $this->mundo->heroi('ranger', ['ouro' => 0]);
        $fase = $this->mundo->fase(['ouro_recompensa' => 20]);

        // ACT
        $r = $this->recompensa->conceder($heroi, $this->desfecho($fase));

        // ASSERT
        $this->assertSame(24, $r['ouro'], 'round(20 * 1.2)');
        $this->assertSame(24, $heroi->refresh()->ouro);
    }

    #[Test]
    public function subir_de_nivel_amplia_e_restaura_vida_e_mana(): void
    {
        // ARRANGE: mago machucado, a um XP de fechar o nível 2 (exige 100).
        $heroi = $this->mundo->heroi('mago', ['xp' => 0, 'hp_atual' => 10, 'mp_atual' => 3]);
        $fase = $this->mundo->fase(['xp_recompensa' => 100]);

        // ACT
        $r = $this->recompensa->conceder($heroi, $this->desfecho($fase));

        // ASSERT
        $this->assertSame(1, $r['niveis']);
        $this->assertSame(2, $r['nivel']);

        $heroi->refresh();
        $this->assertSame(95, $heroi->hp_max, '80 + 15');
        $this->assertSame(68, $heroi->mp_max, '60 + 8');
        $this->assertSame(95, $heroi->hp_atual, 'subir de nível cura por completo');
        $this->assertSame(68, $heroi->mp_atual);
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
        $this->assertSame($estrelas, (new ServicoDeProgressao)->calcularEstrelas($erros, $usouIa));
    }

    #[Test]
    public function vencer_usando_a_ia_nao_sobe_a_reputacao(): void
    {
        // ARRANGE
        $heroi = $this->mundo->heroi('mago', ['reputacao' => -10]);
        $fase = $this->mundo->fase();

        // ACT
        $r = $this->recompensa->conceder($heroi, $this->desfecho($fase, usouIa: true));

        // ASSERT
        $this->assertSame(1, $r['estrelas']);
        $this->assertSame(-10, $heroi->refresh()->reputacao);
    }

    #[Test]
    public function o_progresso_da_fase_guarda_estrelas_acertos_e_a_marca_da_ia(): void
    {
        // ARRANGE
        $heroi = $this->mundo->heroi('mago');
        $fase = $this->mundo->fase();

        // ACT
        $this->recompensa->conceder($heroi, $this->desfecho($fase, erros: 1, acertos: 3));

        // ASSERT
        $linha = ProgressoFase::mapaDoPersonagem($heroi->id)[$fase->id];
        $this->assertSame(2, $linha->estrelas, 'um erro → 2 estrelas');
        $this->assertSame(3, $linha->acertos);
        $this->assertSame(1, $linha->erros);
        $this->assertFalse($linha->usou_ia);
    }

    #[Test]
    public function rejogar_uma_fase_mantem_as_melhores_estrelas_mas_limpa_a_marca_da_ia(): void
    {
        // ARRANGE: primeira passagem colando; segunda, limpa.
        $heroi = $this->mundo->heroi('mago');
        $fase = $this->mundo->fase();

        // ACT
        $this->recompensa->conceder($heroi, $this->desfecho($fase, usouIa: true));  // 1 estrela
        $this->recompensa->conceder($heroi, $this->desfecho($fase));                // 3 estrelas

        // ASSERT: as estrelas acumulam o melhor de sempre; a mancha da IA, não.
        // Redenção é regra do jogo — ver ProgressoFase::registrar.
        $linha = ProgressoFase::mapaDoPersonagem($heroi->id)[$fase->id];
        $this->assertSame(3, $linha->estrelas);
        $this->assertFalse($linha->usou_ia, 'rejogar sem colar apaga a mancha');
    }

    #[Test]
    public function o_drop_da_fase_entra_no_inventario_uma_unica_vez(): void
    {
        // ARRANGE
        $heroi = $this->mundo->heroi('mago');
        $espada = $this->mundo->item('arma', ['ataque' => 5]);
        $fase = $this->mundo->fase(['item_drop_id' => $espada->id]);

        // ACT: vence a mesma fase duas vezes (o jogo permite rejogar).
        $primeira = $this->recompensa->conceder($heroi, $this->desfecho($fase));
        $segunda = $this->recompensa->conceder($heroi, $this->desfecho($fase));

        // ASSERT
        $this->assertNotNull($primeira['item_drop']);
        $this->assertNull($segunda['item_drop'], 'quem já tem o item não recebe de novo');
        $this->assertSame(1, ItemDoInventario::quantidade($heroi->id, $espada->id));
    }

    #[Test]
    public function conceder_a_mesma_batalha_duas_vezes_nao_credita_de_novo(): void
    {
        // A dívida que a Fase 2 pagou. No legado, a guarda contra duplo-crédito
        // era o flag `recompensado` de $_SESSION['batalha'] — durava o que durava
        // a sessão e não valia nada contra requisições concorrentes. Chamar
        // conceder() duas vezes duplicava a reputação (o teste equivalente do
        // legado registra exatamente isso). Aqui a chave está no banco.

        // ARRANGE
        $heroi = $this->mundo->heroi('mago', ['ouro' => 50, 'xp' => 0, 'reputacao' => 0]);
        $fase = $this->mundo->fase(['xp_recompensa' => 50, 'ouro_recompensa' => 20]);
        $desfecho = $this->desfecho($fase); // mesmo batalhaId nas duas chamadas

        // ACT
        $primeira = $this->recompensa->conceder($heroi, $desfecho);
        $segunda = $this->recompensa->conceder($heroi, $desfecho);

        // ASSERT
        $this->assertNotNull($primeira);
        $this->assertNull($segunda, 'a segunda concessão é recusada pela chave de idempotência');

        $heroi->refresh();
        $this->assertSame(70, $heroi->ouro, 'creditado uma vez');
        $this->assertSame(50, $heroi->xp);
        $this->assertSame(5, $heroi->reputacao, 'no legado isto virava 10');

        $this->assertSame(1, DB::table('recompensas_batalha')->count());
    }

    #[Test]
    public function batalhas_diferentes_na_mesma_fase_sao_recompensadas_cada_uma(): void
    {
        // ARRANGE: rejogar é permitido — a idempotência é por batalha, não por fase.
        $heroi = $this->mundo->heroi('mago', ['ouro' => 0]);
        $fase = $this->mundo->fase(['xp_recompensa' => 0, 'ouro_recompensa' => 20]);

        // ACT
        $this->recompensa->conceder($heroi, $this->desfecho($fase));
        $this->recompensa->conceder($heroi, $this->desfecho($fase));

        // ASSERT
        $this->assertSame(40, $heroi->refresh()->ouro);
        $this->assertSame(2, DB::table('recompensas_batalha')->count());
    }

    #[Test]
    public function derrotar_o_chefe_de_uma_regiao_avanca_o_capitulo_e_da_a_conquista(): void
    {
        // ARRANGE: o código da conquista vem de jogo.regioes_mestre, por svg_slug.
        $this->mundo->conquistas('mestre_willen', 'cacador_de_chefes', 'primeiro_passo', 'puro_de_coracao');
        $mestre = $this->mundo->mestre(['svg_slug' => 'mestre-willen', 'ordem' => 1]);
        $heroi = $this->mundo->heroi('mago', ['capitulo' => 0]);
        $fase = $this->mundo->fase(['tipo' => 'chefe', 'mestre_id' => $mestre->id]);

        // ACT
        $r = $this->recompensa->conceder($heroi, $this->desfecho($fase));

        // ASSERT
        $nomes = array_column($r['conquistas'], 'nome');
        $this->assertContains('Mestre willen', $nomes, 'discípulo da região');
        $this->assertContains('Cacador de chefes', $nomes);
        $this->assertContains('Puro de coracao', $nomes, 'região inteira concluída sem IA');
        $this->assertSame(1, $heroi->refresh()->capitulo);
    }

    #[Test]
    public function usar_a_ia_em_qualquer_fase_da_regiao_bloqueia_puro_de_coracao(): void
    {
        // ARRANGE: uma lição já concluída COM IA, depois o chefe vencido sem IA.
        $this->mundo->conquistas('mestre_willen', 'cacador_de_chefes', 'primeiro_passo', 'puro_de_coracao');
        $mestre = $this->mundo->mestre(['svg_slug' => 'mestre-willen', 'ordem' => 1]);
        $heroi = $this->mundo->heroi('mago');
        $licao = $this->mundo->fase(['tipo' => 'licao', 'mestre_id' => $mestre->id, 'ordem_global' => 1]);
        $chefe = $this->mundo->fase(['tipo' => 'chefe', 'mestre_id' => $mestre->id, 'ordem_global' => 2]);

        ProgressoFase::registrar($heroi->id, $licao->id, 1, 2, 0, usouIa: true);

        // ACT
        $r = $this->recompensa->conceder($heroi, $this->desfecho($chefe));

        // ASSERT
        $this->assertNotContains('Puro de coracao', array_column($r['conquistas'], 'nome'));
    }

    /** Cada chamada gera um batalhaId novo, como faz o motor ao iniciar a batalha. */
    private function desfecho(Fase $fase, int $erros = 0, bool $usouIa = false, int $acertos = 4): ResultadoDaBatalha
    {
        return new ResultadoDaBatalha(
            batalhaId: (string) Str::uuid(),
            faseId: $fase->id,
            acertos: $acertos,
            erros: $erros,
            usouIa: $usouIa,
        );
    }
}

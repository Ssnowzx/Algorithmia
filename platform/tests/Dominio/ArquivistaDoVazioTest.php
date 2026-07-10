<?php

declare(strict_types=1);

namespace Tests\Dominio;

use App\Dominio\Progressao\ServicoDeConquistas;
use App\Models\Conquista;
use App\Models\Fase;
use App\Models\Personagem;
use App\Models\ProgressoFase;
use PHPUnit\Framework\Attributes\Test;
use Tests\RefreshDatabase;
use Tests\Suporte\Mundo;
use Tests\TestCase;

/**
 * "Recuperar **todos** os Logs do Zero" — a conquista `arquivista_do_vazio`.
 *
 * **O defeito que isto conserta.** A regra era uma lista de quatro ids fixos, herdada do
 * `ConquistaService.php:86` do legado: 8, 14, 20 e 32. Amarrar uma regra de jogo à chave
 * primária do banco cobrava caro em dois lugares:
 *
 * 1. **os ids são globais.** Uma segunda instituição jamais teria a fase 8, e a conquista
 *    ficava inalcançável nela — em silêncio, e para sempre;
 * 2. **um mestre que criasse uma quinta secundária** pelo painel ganhava uma fase que não
 *    contava, e a conquista continuava saindo com quatro.
 *
 * Agora as secundárias são as fases de `tipo = 'secundaria'` da instituição. No conteúdo do
 * legado são exatamente aquelas quatro, e o comportamento não muda — este arquivo prova as
 * duas coisas.
 */
final class ArquivistaDoVazioTest extends TestCase
{
    use RefreshDatabase;

    private Mundo $mundo;

    private ServicoDeConquistas $conquistas;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mundo = new Mundo;
        $this->conquistas = new ServicoDeConquistas;

        $this->mundo->conquistas('arquivista_do_vazio');
    }

    /** @return list<Fase> */
    private function secundarias(int $quantas): array
    {
        $fases = [];

        for ($i = 0; $i < $quantas; $i++) {
            $fases[] = $this->mundo->fase([
                'nome' => "Eco Perdido {$i}", 'tipo' => 'secundaria', 'ordem_global' => 100 + $i,
            ]);
        }

        return $fases;
    }

    private function concluir(Personagem $heroi, Fase $fase): void
    {
        ProgressoFase::registrar($heroi->id, $fase->id, estrelas: 3, acertos: 3, erros: 0, usouIa: false);
    }

    private function avaliar(Personagem $heroi, Fase $fase): ?Conquista
    {
        $concedidas = $this->conquistas->avaliarAposFase($heroi, $fase, erros: 0, usouIa: false);

        foreach ($concedidas as $conquista) {
            if ($conquista->codigo === 'arquivista_do_vazio') {
                return $conquista;
            }
        }

        return null;
    }

    #[Test]
    public function so_e_concedida_quando_todas_as_secundarias_estao_concluidas(): void
    {
        // ARRANGE
        $heroi = $this->mundo->heroi();
        $fases = $this->secundarias(4);

        // ACT + ASSERT: as três primeiras não bastam.
        foreach (array_slice($fases, 0, 3) as $fase) {
            $this->concluir($heroi, $fase);
            $this->assertNull($this->avaliar($heroi, $fase));
        }

        // A quarta fecha a coleção.
        $this->concluir($heroi, $fases[3]);
        $this->assertNotNull($this->avaliar($heroi, $fases[3]));
    }

    /** Concluir uma lição não pode disparar a avaliação — nem por acidente. */
    #[Test]
    public function concluir_uma_fase_que_nao_e_secundaria_nao_concede_nada(): void
    {
        // ARRANGE: todas as secundárias já feitas.
        $heroi = $this->mundo->heroi();

        foreach ($this->secundarias(4) as $fase) {
            $this->concluir($heroi, $fase);
        }

        $licao = $this->mundo->fase(['nome' => 'Uma lição', 'tipo' => 'licao', 'ordem_global' => 1]);
        $this->concluir($heroi, $licao);

        // ACT + ASSERT
        $this->assertNull($this->avaliar($heroi, $licao));
    }

    /**
     * A regra antiga premiava quatro fases porque quatro números estavam num arquivo de
     * configuração. Um mestre que criasse a quinta secundária pelo painel a via ignorada.
     */
    #[Test]
    public function uma_quinta_secundaria_criada_pelo_mestre_passa_a_contar(): void
    {
        // ARRANGE
        $heroi = $this->mundo->heroi();
        $fases = $this->secundarias(5);

        // ACT: quatro concluídas — o número mágico antigo.
        foreach (array_slice($fases, 0, 4) as $fase) {
            $this->concluir($heroi, $fase);
        }

        // ASSERT: não basta mais. "Todos" quer dizer todos.
        $this->assertNull($this->avaliar($heroi, $fases[3]));

        $this->concluir($heroi, $fases[4]);
        $this->assertNotNull($this->avaliar($heroi, $fases[4]));
    }

    /**
     * O conteúdo do legado tem exatamente quatro secundárias, e são as de id 8, 14, 20 e 32.
     * Trocar a regra de "estes ids" para "este tipo" não muda o jogo de ninguém — é isto que
     * este teste guarda.
     */
    #[Test]
    public function no_conteudo_do_legado_o_comportamento_e_o_mesmo_de_antes(): void
    {
        // ARRANGE: as quatro secundárias, com os ids exatos do legado.
        $heroi = $this->mundo->heroi();

        /** @var list<int> $ids */
        $ids = config('jogo.fases_secundarias');
        $fases = [];

        foreach ($ids as $i => $id) {
            \Illuminate\Support\Facades\DB::table('fases')->insert([
                'id' => $id, 'ordem_global' => 100 + $i, 'nome' => "Eco {$id}", 'tipo' => 'secundaria',
                'inimigo_hp' => 60, 'inimigo_ataque' => 10, 'xp_recompensa' => 50, 'ouro_recompensa' => 20,
            ]);

            $fases[] = Fase::query()->findOrFail($id);
        }

        // ACT
        foreach (array_slice($fases, 0, 3) as $fase) {
            $this->concluir($heroi, $fase);
            $this->assertNull($this->avaliar($heroi, $fase));
        }

        $this->concluir($heroi, $fases[3]);

        // ASSERT
        $this->assertNotNull($this->avaliar($heroi, $fases[3]));
    }
}

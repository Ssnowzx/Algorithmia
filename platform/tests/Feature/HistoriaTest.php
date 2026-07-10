<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Dialogo;
use App\Models\Personagem;
use App\Models\ProgressoFase;
use App\Models\Usuario;
use PHPUnit\Framework\Attributes\Test;
use Tests\RefreshDatabase;
use Tests\Suporte\Mundo;
use Tests\TestCase;

/**
 * As fases de história abrem a campanha. Sem elas o mapa nunca destrava: a fase 1
 * do jogo real é do tipo `historia`, e a 2 exige a 1.
 */
final class HistoriaTest extends TestCase
{
    use RefreshDatabase;

    private Mundo $mundo;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mundo = new Mundo;
    }

    #[Test]
    public function concluir_uma_fase_de_historia_nao_acontece_por_get(): void
    {
        // Este é o furo que o docs/migracao/INVENTARIO.md §1 achou no legado:
        // `historia/concluir` gravava progresso e XP por GET. Um <img src>
        // avançava a campanha de quem só abriu uma página qualquer.

        // ARRANGE
        $heroi = $this->entrarComHeroi();
        $prologo = $this->mundo->fase(['tipo' => 'historia', 'xp_recompensa' => 25]);

        // ACT + ASSERT
        $this->get("/historia/{$prologo->id}/concluir")->assertMethodNotAllowed();

        $this->assertSame(0, $heroi->refresh()->xp);
        $this->assertFalse(ProgressoFase::concluiu($heroi->id, $prologo->id));
    }

    #[Test]
    public function a_cena_mostra_as_falas_e_conduz_ao_proximo_passo(): void
    {
        // ARRANGE
        $this->entrarComHeroi();
        $prologo = $this->mundo->fase(['tipo' => 'historia', 'nome' => 'Prólogo: O Despertar']);
        Dialogo::create([
            'fase_id' => $prologo->id, 'momento' => 'antes', 'variante' => 'padrao',
            'ordem' => 0, 'falante' => 'Narrador', 'texto' => 'O reino silenciou.',
        ]);

        // ACT + ASSERT
        $this->get(route('historia.ver', $prologo))->assertOk()
            ->assertSee('Prólogo: O Despertar')
            ->assertSee('O reino silenciou.')
            ->assertSee('Seguir em frente');
    }

    #[Test]
    public function concluir_grava_progresso_paga_xp_e_destrava_a_fase_seguinte(): void
    {
        // ARRANGE
        $heroi = $this->entrarComHeroi();
        $prologo = $this->mundo->fase(['tipo' => 'historia', 'ordem_global' => 1, 'xp_recompensa' => 25]);
        $seguinte = $this->mundo->fase(['ordem_global' => 2, 'requisito_fase_id' => $prologo->id]);
        $this->mundo->desafio($seguinte->id);

        // A seguinte começa trancada.
        $this->get(route('batalha.iniciar', $seguinte))->assertRedirect(route('mapa'));

        // ACT
        $this->post(route('historia.concluir', $prologo))->assertRedirect(route('mapa'));

        // ASSERT
        $this->assertSame(25, $heroi->refresh()->xp);
        $this->assertSame(3, ProgressoFase::mapaDoPersonagem($heroi->id)[$prologo->id]->estrelas);
        $this->get(route('batalha.iniciar', $seguinte))->assertOk();
    }

    #[Test]
    public function reler_uma_cena_nao_paga_xp_de_novo(): void
    {
        // ARRANGE
        $heroi = $this->entrarComHeroi();
        $prologo = $this->mundo->fase(['tipo' => 'historia', 'xp_recompensa' => 25]);

        // ACT
        $this->post(route('historia.concluir', $prologo));
        $this->post(route('historia.concluir', $prologo));

        // ASSERT
        $this->assertSame(25, $heroi->refresh()->xp);
    }

    #[Test]
    public function nao_se_conclui_uma_fase_de_historia_trancada(): void
    {
        // ARRANGE
        $heroi = $this->entrarComHeroi();
        $primeira = $this->mundo->fase(['tipo' => 'historia', 'ordem_global' => 1]);
        $trancada = $this->mundo->fase([
            'tipo' => 'historia', 'ordem_global' => 2,
            'requisito_fase_id' => $primeira->id, 'xp_recompensa' => 999,
        ]);

        // ACT
        $this->post(route('historia.concluir', $trancada))
            ->assertRedirect(route('mapa'))
            ->assertSessionHas('erro');

        // ASSERT
        $this->assertSame(0, $heroi->refresh()->xp);
    }

    #[Test]
    public function concluir_recusa_fases_que_nao_sao_de_historia(): void
    {
        // ARRANGE: uma lição se vence lutando, não clicando em "seguir".
        $heroi = $this->entrarComHeroi();
        $licao = $this->mundo->fase(['tipo' => 'licao', 'xp_recompensa' => 999]);

        // ACT + ASSERT
        $this->post(route('historia.concluir', $licao))->assertRedirect(route('mapa'));
        $this->assertSame(0, $heroi->refresh()->xp);
        $this->assertFalse(ProgressoFase::concluiu($heroi->id, $licao->id));
    }

    #[Test]
    public function o_heroi_de_reputacao_baixa_ouve_a_variante_da_ia(): void
    {
        // ARRANGE: variante 'ia' a partir de reputação ≤ -20.
        $heroi = $this->entrarComHeroi();
        $heroi->update(['reputacao' => -30]);
        $fase = $this->mundo->fase(['tipo' => 'historia']);

        foreach ([['padrao', 'Você trilhou o caminho difícil.'], ['ia', 'O Fragmento sussurra que você já sabe.']] as [$variante, $texto]) {
            Dialogo::create([
                'fase_id' => $fase->id, 'momento' => 'antes', 'variante' => $variante,
                'ordem' => 0, 'falante' => 'Narrador', 'texto' => $texto,
            ]);
        }

        // ACT + ASSERT
        $this->get(route('historia.ver', $fase))->assertOk()
            ->assertSee('O Fragmento sussurra que você já sabe.')
            ->assertDontSee('Você trilhou o caminho difícil.');
    }

    #[Test]
    public function sem_a_variante_da_ia_escrita_a_cena_cai_para_a_padrao(): void
    {
        // ARRANGE: sem o fallback, um herói de reputação baixa veria a fase muda.
        $heroi = $this->entrarComHeroi();
        $heroi->update(['reputacao' => -50]);
        $fase = $this->mundo->fase(['tipo' => 'historia']);
        Dialogo::create([
            'fase_id' => $fase->id, 'momento' => 'antes', 'variante' => 'padrao',
            'ordem' => 0, 'falante' => 'Narrador', 'texto' => 'A única fala escrita.',
        ]);

        // ACT + ASSERT
        $this->get(route('historia.ver', $fase))->assertOk()->assertSee('A única fala escrita.');
    }

    private function entrarComHeroi(): Personagem
    {
        $heroi = $this->mundo->heroi('mago');
        $this->actingAs(Usuario::query()->findOrFail($heroi->usuario_id));

        return $heroi;
    }
}

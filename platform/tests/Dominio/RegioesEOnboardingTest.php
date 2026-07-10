<?php

declare(strict_types=1);

namespace Tests\Dominio;

use App\Dominio\Progressao\ServicoDeOnboarding;
use App\Dominio\Progressao\ServicoDeRegioes;
use App\Models\ProgressoFase;
use PHPUnit\Framework\Attributes\Test;
use Tests\RefreshDatabase;
use Tests\Suporte\Mundo;
use Tests\TestCase;

/**
 * Domínio das regiões (a maestria horizontal) e os Primeiros Passos.
 *
 * "Dominada" exige perfeição: 3 estrelas em cada fase principal, o que só
 * acontece sem um único erro e sem tocar no Fragmento da IA.
 */
final class RegioesEOnboardingTest extends TestCase
{
    use RefreshDatabase;

    private Mundo $mundo;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mundo = new Mundo;
    }

    #[Test]
    public function uma_regiao_intocada_esta_a_explorar(): void
    {
        $faixa = ServicoDeRegioes::faixaDe(total: 4, concluidas: 0, perfeitas: 0, estrelas: 0);

        $this->assertSame('a_explorar', $faixa['chave']);
        $this->assertSame('Entre na região', $faixa['dica']);
        $this->assertSame(0, $faixa['pct']);
        $this->assertFalse($faixa['dominada']);
    }

    #[Test]
    public function faltando_fases_a_regiao_esta_em_jornada(): void
    {
        $faixa = ServicoDeRegioes::faixaDe(total: 4, concluidas: 2, perfeitas: 2, estrelas: 6);

        $this->assertSame('em_jornada', $faixa['chave']);
        $this->assertSame('Faltam 2 fases', $faixa['dica']);
        $this->assertSame(50, $faixa['pct'], '6 de 12 estrelas possíveis');
    }

    #[Test]
    public function o_singular_da_dica_e_respeitado(): void
    {
        $faixa = ServicoDeRegioes::faixaDe(total: 2, concluidas: 1, perfeitas: 1, estrelas: 3);

        $this->assertSame('Faltam 1 fase', $faixa['dica']);
    }

    #[Test]
    public function concluir_tudo_sem_perfeicao_apenas_conquista_a_regiao(): void
    {
        // Todas as fases feitas, mas nem todas com 3 estrelas.
        $faixa = ServicoDeRegioes::faixaDe(total: 3, concluidas: 3, perfeitas: 1, estrelas: 7);

        $this->assertSame('conquistada', $faixa['chave']);
        $this->assertSame('Perfeccione 2 p/ dominar', $faixa['dica']);
        $this->assertFalse($faixa['dominada']);
    }

    #[Test]
    public function a_perfeicao_total_domina_a_regiao(): void
    {
        $faixa = ServicoDeRegioes::faixaDe(total: 3, concluidas: 3, perfeitas: 3, estrelas: 9);

        $this->assertSame('dominada', $faixa['chave']);
        $this->assertTrue($faixa['dominada']);
        $this->assertSame(100, $faixa['pct']);
        $this->assertSame('Domínio total 👑', $faixa['dica']);
    }

    #[Test]
    public function o_dominio_le_apenas_licoes_e_chefes_da_regiao(): void
    {
        // As secundárias são opcionais: não podem impedir o domínio.

        // ARRANGE
        $heroi = $this->mundo->heroi('mago');
        $mestre = $this->mundo->mestre(['regiao' => 'Porto da Sintaxe']);

        $licao = $this->mundo->fase(['mestre_id' => $mestre->id, 'tipo' => 'licao', 'ordem_global' => 1]);
        $chefe = $this->mundo->fase(['mestre_id' => $mestre->id, 'tipo' => 'chefe', 'ordem_global' => 2]);
        $this->mundo->fase(['mestre_id' => $mestre->id, 'tipo' => 'secundaria', 'ordem_global' => 3]);

        ProgressoFase::registrar($heroi->id, $licao->id, 3, 4, 0, usouIa: false);
        ProgressoFase::registrar($heroi->id, $chefe->id, 3, 5, 0, usouIa: false);

        // ACT
        $dominio = ServicoDeRegioes::dominio($heroi->id);

        // ASSERT: 2 fases principais, ambas perfeitas — apesar da secundária intocada.
        $this->assertCount(1, $dominio);
        $this->assertSame('Porto da Sintaxe', $dominio[0]['regiao']);
        $this->assertSame(2, $dominio[0]['faixa']['total']);
        $this->assertTrue($dominio[0]['faixa']['dominada']);
        $this->assertSame(1, ServicoDeRegioes::totalDominadas($dominio));
    }

    #[Test]
    public function o_titulo_de_lenda_so_vem_com_todas_as_regioes_dominadas(): void
    {
        // ARRANGE
        $heroi = $this->mundo->heroi('mago');

        $porto = $this->mundo->mestre(['regiao' => 'Porto', 'svg_slug' => 'mestre-willen', 'ordem' => 1]);
        $torre = $this->mundo->mestre(['regiao' => 'Torre', 'svg_slug' => 'mestre-cassandro', 'ordem' => 2]);

        $faseA = $this->mundo->fase(['mestre_id' => $porto->id, 'tipo' => 'chefe', 'ordem_global' => 1]);
        $faseB = $this->mundo->fase(['mestre_id' => $torre->id, 'tipo' => 'chefe', 'ordem_global' => 2]);

        ProgressoFase::registrar($heroi->id, $faseA->id, 3, 5, 0, usouIa: false);

        // ACT + ASSERT: uma região dominada, a outra não.
        $this->assertSame('', ServicoDeRegioes::tituloLenda(ServicoDeRegioes::dominio($heroi->id)));

        // ACT: agora as duas.
        ProgressoFase::registrar($heroi->id, $faseB->id, 3, 5, 0, usouIa: false);

        // ASSERT
        $this->assertSame(
            config('jogo.regiao_titulo_lenda'),
            ServicoDeRegioes::tituloLenda(ServicoDeRegioes::dominio($heroi->id))
        );
    }

    #[Test]
    public function sem_regiao_alguma_nao_ha_titulo_de_lenda(): void
    {
        // Um herói num mundo vazio não é o Mestre dos Cinco.
        $this->assertSame('', ServicoDeRegioes::tituloLenda([]));
    }

    #[Test]
    public function o_primeiro_passo_do_onboarding_ja_nasce_feito(): void
    {
        // Endowed progress: uma jornada que começou tem mais chance de terminar.

        // ARRANGE + ACT
        $painel = ServicoDeOnboarding::montar(['batalha' => false, 'item' => false, 'conquista' => false], nivel: 1);

        // ASSERT
        $this->assertSame(1, $painel['completos']);
        $this->assertSame(4, $painel['total']);
        $this->assertTrue($painel['passos'][0]['feito'], 'forjar o herói já está feito');
        $this->assertTrue($painel['mostrar']);
    }

    #[Test]
    public function o_painel_some_quando_a_lista_termina(): void
    {
        $painel = ServicoDeOnboarding::montar(['batalha' => true, 'item' => true, 'conquista' => true], nivel: 1);

        $this->assertSame(4, $painel['completos']);
        $this->assertFalse($painel['mostrar']);
    }

    #[Test]
    public function o_painel_some_quando_o_heroi_cresce(): void
    {
        // Um checklist de novato exibido ao veterano é ruído.
        $painel = ServicoDeOnboarding::montar(
            ['batalha' => false, 'item' => false, 'conquista' => false],
            nivel: (int) config('jogo.onboarding_nivel_max') + 1
        );

        $this->assertFalse($painel['mostrar']);
    }

    #[Test]
    public function os_primeiros_passos_leem_o_estado_real_do_heroi(): void
    {
        // ARRANGE
        $this->mundo->conquistas('primeiro_passo');
        $heroi = $this->mundo->heroi('mago');
        $fase = $this->mundo->fase();

        // ACT + ASSERT: nada feito além de existir.
        $this->assertSame(1, ServicoDeOnboarding::primeirosPassos($heroi)['completos']);

        // ACT: venceu uma fase e equipou um item.
        ProgressoFase::registrar($heroi->id, $fase->id, 3, 4, 0, usouIa: false);
        $this->mundo->darItem($heroi, $this->mundo->item('arma', ['ataque' => 3]), equipado: true);

        // ASSERT
        $this->assertSame(3, ServicoDeOnboarding::primeirosPassos($heroi->refresh())['completos']);
    }
}

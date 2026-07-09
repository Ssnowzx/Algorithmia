<?php

declare(strict_types=1);

namespace Tests\Dominio;

use App\Dominio\Progressao\ServicoDeMaestria;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

/**
 * Maestria por matéria.
 *
 * Domínio é volume de acertos **mais** precisão sustentada. Se fosse só a
 * porcentagem bruta, "Mestre" seria alcançável com três respostas e sorte.
 *
 * Faixas: 0 Não iniciado · 1 Iniciante · 2 Aprendiz (3 acertos)
 *         3 Praticante (6 acertos, 60%) · 4 Especialista (10, 75%) · 5 Mestre (15, 85%)
 */
final class MaestriaTest extends TestCase
{
    #[TestWith([0, 0, 0, 'Não iniciado'])]
    #[TestWith([1, 0, 1, 'Iniciante'])]
    #[TestWith([5, 3, 2, 'Aprendiz'])]
    #[TestWith([10, 6, 3, 'Praticante'])]
    #[TestWith([13, 10, 4, 'Especialista'])]
    #[TestWith([17, 15, 5, 'Mestre'])]
    #[Test]
    public function a_faixa_depende_de_acertos_e_de_precisao(int $total, int $acertos, int $tier, string $rotulo): void
    {
        $faixa = ServicoDeMaestria::faixaDe($total, $acertos);

        $this->assertSame($tier, $faixa['tier']);
        $this->assertSame($rotulo, $faixa['rotulo']);
    }

    #[Test]
    public function muitos_acertos_com_precisao_baixa_nao_sobem_de_faixa(): void
    {
        // 20 acertos em 40 respostas = 50%, abaixo do piso de 60% do Praticante.
        $faixa = ServicoDeMaestria::faixaDe(total: 40, acertos: 20);

        $this->assertSame(2, $faixa['tier'], 'trava no Aprendiz, que não exige piso');
        $this->assertSame(50, $faixa['precisao']);
        $this->assertFalse($faixa['dominada']);
    }

    #[Test]
    public function precisao_perfeita_com_poucos_acertos_tambem_nao_sobe(): void
    {
        // 100% de precisão, mas só 2 acertos: nem o Aprendiz (3) é alcançado.
        $faixa = ServicoDeMaestria::faixaDe(total: 2, acertos: 2);

        $this->assertSame(1, $faixa['tier']);
        $this->assertSame(100, $faixa['precisao']);
    }

    #[Test]
    public function a_barra_reflete_o_fator_mais_atrasado_e_nunca_enche_enganando(): void
    {
        // 12 acertos em 20 (60%) rumo a Especialista, que exige 10 acertos e 75%.
        // Os acertos já bastam (120%), mas a precisão está em 60/75 = 80%.
        $faixa = ServicoDeMaestria::faixaDe(total: 20, acertos: 12);

        $this->assertSame('Especialista', $faixa['proximo']['rotulo']);
        $this->assertSame(80, $faixa['proximo']['pct'], 'a barra mede a precisão, que é o gargalo');
        $this->assertStringContainsString('75%', $faixa['proximo']['dica']);
    }

    #[Test]
    public function faltando_acertos_a_dica_diz_quantos(): void
    {
        // ARRANGE + ACT
        $faixa = ServicoDeMaestria::faixaDe(total: 1, acertos: 1);

        // ASSERT: 2 acertos para o Aprendiz.
        $this->assertSame('Faltam 2 p/ Aprendiz', $faixa['proximo']['dica']);
    }

    #[Test]
    public function no_topo_nao_ha_proximo_tier(): void
    {
        $faixa = ServicoDeMaestria::faixaDe(total: 20, acertos: 20);

        $this->assertTrue($faixa['maximo']);
        $this->assertNull($faixa['proximo']);
    }

    #[Test]
    public function acertos_nunca_excedem_o_total(): void
    {
        // Entrada corrompida não pode gerar precisão acima de 100%.
        $faixa = ServicoDeMaestria::faixaDe(total: 5, acertos: 99);

        $this->assertSame(5, $faixa['acertos']);
        $this->assertSame(100, $faixa['precisao']);
    }

    #[Test]
    public function sem_respostas_a_meta_e_simplesmente_comecar(): void
    {
        $faixa = ServicoDeMaestria::faixaDe(0, 0);

        $this->assertSame('Responda para começar', $faixa['proximo']['dica']);
        $this->assertSame(0, $faixa['proximo']['pct']);
    }

    #[Test]
    public function as_oito_materias_sempre_aparecem_mesmo_sem_resposta(): void
    {
        // ARRANGE + ACT
        $maestria = ServicoDeMaestria::porMateria(['php' => ['total' => 20, 'acertos' => 18]]);

        // ASSERT
        $this->assertCount(8, $maestria);
        $this->assertSame('Mestre', $maestria['php']['faixa']['rotulo']);
        $this->assertSame('Não iniciado', $maestria['calculo']['faixa']['rotulo']);
        $this->assertSame(1, ServicoDeMaestria::totalDominadas($maestria));
    }

    #[Test]
    public function dominada_comeca_no_especialista(): void
    {
        // ARRANGE: maestria_tier_dominada = 4 (Especialista).
        $this->assertFalse(ServicoDeMaestria::faixaDe(10, 6)['dominada'], 'Praticante ainda não domina');
        $this->assertTrue(ServicoDeMaestria::faixaDe(13, 10)['dominada'], 'Especialista domina');
    }
}

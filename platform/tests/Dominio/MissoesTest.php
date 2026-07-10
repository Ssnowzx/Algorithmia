<?php

declare(strict_types=1);

namespace Tests\Dominio;

use App\Dominio\Progressao\ServicoDeMissoes;
use App\Models\ProgressoFase;
use App\Models\RespostaLog;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\Test;
use Tests\RefreshDatabase;
use Tests\Suporte\Mundo;
use Tests\TestCase;

/**
 * Missões da semana.
 *
 * Sem cron e sem persistência: a seleção é determinística pela semana ISO, e o
 * progresso é derivado do log de respostas na hora de mostrar.
 */
final class MissoesTest extends TestCase
{
    use RefreshDatabase;

    private Mundo $mundo;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mundo = new Mundo;
    }

    #[Test]
    public function a_mesma_semana_sorteia_sempre_o_mesmo_conjunto(): void
    {
        // ARRANGE + ACT
        $primeira = ServicoDeMissoes::selecionar(107_000);
        $segunda = ServicoDeMissoes::selecionar(107_000);

        // ASSERT
        $this->assertSame($primeira, $segunda);
        $this->assertCount((int) config('jogo.missoes_por_semana'), $primeira);
    }

    #[Test]
    public function semanas_diferentes_sorteiam_conjuntos_diferentes(): void
    {
        $a = array_column(ServicoDeMissoes::selecionar(100), 'codigo');
        $b = array_column(ServicoDeMissoes::selecionar(101), 'codigo');

        $this->assertNotSame($a, $b);
    }

    #[Test]
    public function um_indice_negativo_nao_estoura_o_pool(): void
    {
        // O módulo duplo existe para isto: `-1 % 8` é -1 em PHP.
        $missoes = ServicoDeMissoes::selecionar(-1);

        $this->assertCount(3, $missoes);
        foreach ($missoes as $missao) {
            $this->assertArrayHasKey('codigo', $missao);
        }
    }

    #[Test]
    public function o_indice_da_semana_muda_de_uma_semana_para_a_outra(): void
    {
        // ARRANGE: quarta-feira e a quarta seguinte.
        Date::setTestNow('2026-07-08 12:00:00');
        $agora = ServicoDeMissoes::indiceSemanaAtual();

        Date::setTestNow('2026-07-15 12:00:00');
        $proxima = ServicoDeMissoes::indiceSemanaAtual();

        // ASSERT
        $this->assertSame($agora + 1, $proxima);
        Date::setTestNow();
    }

    #[Test]
    public function uma_missao_contavel_mede_progresso_e_completa_no_alvo(): void
    {
        // ARRANGE
        $missao = ['codigo' => 'maratona', 'titulo' => 'Maratona', 'icone' => '🏃', 'metrica' => 'respostas', 'alvo' => 20, 'desc' => ''];

        // ACT
        $meio = ServicoDeMissoes::avaliar($missao, $this->metricas(['respostas' => 10]));
        $cheia = ServicoDeMissoes::avaliar($missao, $this->metricas(['respostas' => 25]));

        // ASSERT
        $this->assertSame(50, $meio['pct']);
        $this->assertFalse($meio['completa']);

        $this->assertSame(100, $cheia['pct'], 'a barra satura em 100');
        $this->assertTrue($cheia['completa']);
    }

    #[Test]
    public function a_missao_de_precisao_mede_volume_ate_o_minimo_e_so_entao_precisao(): void
    {
        // Uma barra de precisão cheia com duas respostas não mediria nada.

        // ARRANGE: 80% de acerto em pelo menos 10 respostas.
        $missao = ['codigo' => 'mente_afiada', 'titulo' => 'Mente Afiada', 'icone' => '🧠',
            'metrica' => 'precisao', 'alvo' => 80, 'min' => 10, 'desc' => ''];

        // ACT: volume insuficiente, ainda que 100% de acerto.
        $cedo = ServicoDeMissoes::avaliar($missao, $this->metricas(['respostas' => 4, 'acertos' => 4]));

        // ASSERT: a barra mede o volume, não a precisão.
        $this->assertSame('responda 10 p/ valer', $cedo['nota']);
        $this->assertSame('respostas', $cedo['unidade']);
        $this->assertSame(4, $cedo['atual']);
        $this->assertSame(40, $cedo['pct']);
        $this->assertFalse($cedo['completa']);

        // ACT: volume atingido, precisão suficiente.
        $pronto = ServicoDeMissoes::avaliar($missao, $this->metricas(['respostas' => 10, 'acertos' => 9]));

        // ASSERT
        $this->assertSame('%', $pronto['unidade']);
        $this->assertSame(90, $pronto['atual']);
        $this->assertTrue($pronto['completa']);

        // ACT: volume atingido, precisão insuficiente.
        $fraco = ServicoDeMissoes::avaliar($missao, $this->metricas(['respostas' => 20, 'acertos' => 10]));
        $this->assertSame(50, $fraco['atual']);
        $this->assertFalse($fraco['completa']);
    }

    #[Test]
    public function as_metricas_da_semana_ignoram_o_que_aconteceu_antes(): void
    {
        // O legado usava YEARWEEK(x, 3); no PostgreSQL é date_trunc('week', …),
        // que também começa na segunda-feira.

        // ARRANGE
        Date::setTestNow('2026-07-08 12:00:00'); // quarta-feira
        $heroi = $this->mundo->heroi('mago');
        $fase = $this->mundo->fase();
        $desafio = $this->mundo->desafio($fase->id);

        // Duas respostas nesta semana, uma na semana passada.
        RespostaLog::registrar($heroi->id, $desafio->id, true, false);
        RespostaLog::registrar($heroi->id, $desafio->id, false, true);
        DB::table('respostas_log')->insert([
            'personagem_id' => $heroi->id, 'desafio_id' => $desafio->id,
            'correta' => true, 'usou_ia' => false, 'respondido_em' => '2026-06-30 12:00:00',
        ]);

        // ACT
        $metricas = RespostaLog::metricasSemana($heroi->id);

        // ASSERT
        $this->assertSame(2, $metricas['respostas'], 'a resposta da semana passada não conta');
        $this->assertSame(1, $metricas['acertos']);
        $this->assertSame(1, $metricas['respostas_sem_ia']);
        $this->assertSame(1, $metricas['acertos_sem_ia']);
        $this->assertSame(1, $metricas['materias']);

        Date::setTestNow();
    }

    #[Test]
    public function as_fases_da_semana_alimentam_a_missao_de_avanco(): void
    {
        // ARRANGE
        Date::setTestNow('2026-07-08 12:00:00');
        $heroi = $this->mundo->heroi('mago');
        $atual = $this->mundo->fase(['ordem_global' => 1]);
        $antiga = $this->mundo->fase(['ordem_global' => 2]);

        ProgressoFase::registrar($heroi->id, $atual->id, 3, 4, 0, usouIa: false);
        DB::table('progresso_fases')->insert([
            'personagem_id' => $heroi->id, 'fase_id' => $antiga->id,
            'estrelas' => 3, 'acertos' => 4, 'erros' => 0, 'usou_ia' => false,
            'concluida_em' => '2026-06-30 12:00:00',
        ]);

        // ACT + ASSERT
        $this->assertSame(1, ProgressoFase::fasesSemana($heroi->id));
        Date::setTestNow();
    }

    #[Test]
    public function as_missoes_da_semana_saem_avaliadas_para_um_personagem(): void
    {
        // ARRANGE
        $heroi = $this->mundo->heroi('mago');

        // ACT
        $missoes = ServicoDeMissoes::daSemana($heroi->id);

        // ASSERT: sem atividade, nenhuma completa.
        $this->assertCount(3, $missoes);
        $this->assertSame(0, ServicoDeMissoes::totalCompletas($missoes));
        foreach ($missoes as $missao) {
            $this->assertSame(0, $missao['pct']);
        }
    }

    /**
     * @param  array<string,int>  $sobrescritas
     * @return array{respostas:int,acertos:int,respostas_sem_ia:int,acertos_sem_ia:int,materias:int,fases:int}
     */
    private function metricas(array $sobrescritas = []): array
    {
        /** @var array{respostas:int,acertos:int,respostas_sem_ia:int,acertos_sem_ia:int,materias:int,fases:int} $metricas */
        $metricas = array_merge([
            'respostas' => 0, 'acertos' => 0, 'respostas_sem_ia' => 0,
            'acertos_sem_ia' => 0, 'materias' => 0, 'fases' => 0,
        ], $sobrescritas);

        return $metricas;
    }
}

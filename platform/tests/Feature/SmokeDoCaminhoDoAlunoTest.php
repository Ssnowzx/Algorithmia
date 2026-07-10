<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\Test;
use Tests\RefreshDatabase;
use Tests\Suporte\Mundo;
use Tests\TestCase;

/**
 * Etapa E.3: o smoke percorre o caminho do aluno até o relatório do professor.
 *
 * Antes ele parava na batalha, que roda **em memória**. Nada do que a instituição escreve
 * era exercitado: um `tenant_id` que perdesse o `DEFAULT`, uma policy de RLS mal escrita em
 * `progresso_fases` ou `matriculas`, uma agregação de relatório quebrada — tudo isso passava
 * pelo portão do deploy sem um arranhão, e aparecia na primeira aula.
 *
 * O smoke é o portão do `bin/deploy.sh`: o que ele não prova, o deploy promove.
 */
final class SmokeDoCaminhoDoAlunoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // O smoke checa `APP_DEBUG` e o driver de sessão, e o ambiente de teste tem os
        // dois "errados". Aqui interessa o caminho do aluno, não essas duas.
        config(['app.debug' => false, 'session.driver' => 'database']);

        (new Mundo)->mundoDoSmoke();
    }

    #[Test]
    public function o_smoke_completo_aprova_uma_instituicao_semeada(): void
    {
        // ARRANGE + ACT + ASSERT: as três verificações novas aparecem, e passam.
        $this->artisan('algorithmia:smoke')
            ->expectsOutputToContain('O host do APP_URL resolve uma instituição ativa')
            ->expectsOutputToContain('A instituição tem domínio primário')
            ->expectsOutputToContain('O motor joga uma fase real')
            ->expectsOutputToContain('O progresso do aluno persiste')
            ->expectsOutputToContain('O relatório de turma responde')
            ->assertSuccessful();
    }

    /** O smoke escreve para provar, e desfaz tudo. Um smoke que suja o banco não roda em produção. */
    #[Test]
    public function o_smoke_nao_deixa_nada_no_banco(): void
    {
        // ARRANGE
        $antes = [
            'usuarios' => DB::table('usuarios')->count(),
            'personagens' => DB::table('personagens')->count(),
            'progresso_fases' => DB::table('progresso_fases')->count(),
            'turmas' => DB::table('turmas')->count(),
            'matriculas' => DB::table('matriculas')->count(),
        ];

        // ACT
        $this->artisan('algorithmia:smoke')->assertSuccessful();

        // ASSERT
        foreach ($antes as $tabela => $contagem) {
            $this->assertSame($contagem, DB::table($tabela)->count(), "o smoke deixou lixo em {$tabela}");
        }
    }

    /**
     * O pior sintoma que esta instalação sabe produzir: 404 com o banco cheio e o
     * healthcheck verde. O `/healthz` fica fora do resolvedor de propósito, e por isso não
     * enxerga isto. Ver `RUNBOOK §10.1`.
     */
    #[Test]
    public function um_app_url_que_nao_resolve_instituicao_nenhuma_reprova_o_deploy(): void
    {
        // ARRANGE: nenhum `tenant_dominios` aponta para este host.
        config(['app.url' => 'https://dominio-que-ninguem-cadastrou.test']);

        // ACT + ASSERT
        $this->artisan('algorithmia:smoke --sem-conteudo')
            ->expectsOutputToContain('nenhuma instituição ativa atende')
            ->assertFailed();
    }

    #[Test]
    public function o_app_url_e_comparado_sem_diferenciar_maiusculas(): void
    {
        // ARRANGE: o índice de `tenant_dominios` é sobre `lower(host)`.
        config(['app.url' => 'http://LOCALHOST']);

        // ACT + ASSERT
        $this->artisan('algorithmia:smoke --sem-conteudo')->assertSuccessful();
    }

    #[Test]
    public function a_opcao_rasa_pula_o_caminho_do_aluno_e_diz_isso(): void
    {
        // ARRANGE + ACT + ASSERT: a mensagem final tem de dizer o que NÃO foi provado.
        $this->artisan('algorithmia:smoke --rasa')
            ->expectsOutputToContain('Motor, progresso e relatório NÃO foram exercitados')
            ->doesntExpectOutputToContain('O progresso do aluno persiste')
            ->assertSuccessful();
    }
}

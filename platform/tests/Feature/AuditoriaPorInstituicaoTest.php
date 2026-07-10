<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Dominio\Operacao\ServicoDeAuditoria;
use App\Dominio\Tenancy\ContextoDoTenant;
use App\Models\RegistroDeAuditoria;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\Test;
use Tests\RefreshDatabase;
use Tests\TestCase;

/**
 * A trilha de auditoria pertence a uma instituição — ou à plataforma, e a nenhuma.
 *
 * **O defeito que isto conserta.** `auditoria` nasceu antes da tenancy e ficou para trás: sem
 * `tenant_id`, sem RLS. Não vazava, porque nenhuma rota a lê. Mas as linhas de duas escolas
 * conviviam numa tabela sem barreira, e a primeira tela que as mostrasse as misturaria. Uma
 * trilha de auditoria que não sabe de quem é não é uma trilha de auditoria.
 *
 * **Por que ela ficou para trás.** Há dois tipos de autor, em mundos diferentes: o usuário de
 * uma escola age dentro de um contexto; o operador da plataforma age fora de qualquer um — o
 * `/console` roda sem `ResolverTenant`. Uma policy `tenant_id = current_setting(...)` recusaria
 * a escrita do operador, porque `NULL = NULL` é NULL. A resposta é `IS NOT DISTINCT FROM`, que
 * trata NULL como um valor: a linha da plataforma existe, e só a plataforma a vê.
 */
final class AuditoriaPorInstituicaoTest extends TestCase
{
    use RefreshDatabase;

    private int $escolaB;

    protected function setUp(): void
    {
        parent::setUp();

        $this->escolaB = (int) DB::connection('pgsql_dono')->table('tenants')->insertGetId([
            'nome' => 'Escola B', 'slug' => 'escola-b', 'ativo' => true,
            'created_at' => now(), 'updated_at' => now(),
        ]);
    }

    protected function tearDown(): void
    {
        if (DB::transactionLevel() > 0) {
            DB::rollBack();
        }

        $dono = DB::connection('pgsql_dono');
        $ids = $dono->table('tenants')->where('slug', 'escola-b')->pluck('id');

        $dono->table('auditoria')->whereIn('tenant_id', $ids)->delete();
        $dono->table('tenants')->whereIn('id', $ids)->delete();

        DB::beginTransaction();

        parent::tearDown();
    }

    private function contexto(): ContextoDoTenant
    {
        return app(ContextoDoTenant::class);
    }

    private function registrar(string $acao): RegistroDeAuditoria
    {
        return app(ServicoDeAuditoria::class)->registrar($acao, 'fase', 1, ['nome' => $acao]);
    }

    // ------------------------------------------------------------------ a barreira

    #[Test]
    public function uma_escola_nao_ve_a_auditoria_da_outra(): void
    {
        // ARRANGE: o `TestCase` deixa o contexto na instituição padrão.
        $this->registrar('fase.excluir');

        $this->contexto()->definirNaTransacao($this->escolaB);
        $this->registrar('item.excluir');

        // ACT + ASSERT
        $this->assertSame(1, RegistroDeAuditoria::query()->count());
        $this->assertSame('item.excluir', RegistroDeAuditoria::query()->firstOrFail()->acao);
    }

    #[Test]
    public function uma_query_crua_na_auditoria_nao_escapa_da_policy(): void
    {
        // ARRANGE
        $this->registrar('fase.excluir');

        $this->contexto()->definirNaTransacao($this->escolaB);
        $this->registrar('item.excluir');

        // ACT: sem `WHERE`, e sem Eloquent.
        $linhas = DB::select('SELECT * FROM auditoria');

        // ASSERT
        $this->assertCount(1, $linhas);
        $this->assertSame($this->escolaB, (int) $linhas[0]->tenant_id);
    }

    #[Test]
    public function uma_escola_nao_altera_nem_apaga_a_linha_da_outra(): void
    {
        // ARRANGE
        $daPadrao = $this->registrar('fase.excluir');

        // ACT: a escola B tenta reescrever a história da padrão, sabendo o id.
        $this->contexto()->definirNaTransacao($this->escolaB);

        $alteradas = DB::table('auditoria')->where('id', $daPadrao->id)->update(['acao' => 'nada.aconteceu']);
        $apagadas = DB::table('auditoria')->where('id', $daPadrao->id)->delete();

        // ASSERT: a leitura de volta é pela conexão da APLICAÇÃO, no contexto da padrão. A do
        // dono está fora da transação do teste e não enxergaria a linha — passaria por engano.
        $this->assertSame(0, $alteradas);
        $this->assertSame(0, $apagadas);

        $acao = $this->contexto()->usar(
            $this->tenantPadrao(),
            fn (): ?string => DB::table('auditoria')->where('id', $daPadrao->id)->value('acao'),
        );

        $this->assertSame('fase.excluir', $acao);
    }

    // ---------------------------------------------------- a plataforma, e a sua posição

    /**
     * O operador não pertence a escola nenhuma, e o `/console` roda sem `ResolverTenant`. A
     * linha dele nasce com `tenant_id` NULL — e `NULL` aqui significa "a plataforma", não
     * "esqueceram de preencher".
     */
    #[Test]
    public function a_acao_do_operador_nasce_sem_instituicao(): void
    {
        // ARRANGE: é o estado de uma requisição do console.
        $this->contexto()->limparNaTransacao();

        // ACT
        $linha = $this->registrar('tenant.ativar');

        // ASSERT: a leitura é pela conexão da APLICAÇÃO, ainda sem contexto — é o que o
        // console faz. Pela conexão do dono a linha nem apareceria (ela está na transação do
        // teste), e um `assertNull` passaria pelo motivo errado.
        $recarregada = RegistroDeAuditoria::query()->findOrFail($linha->id);
        $this->assertNull($recarregada->tenant_id);

        // …e a escola padrão não a enxerga.
        $daEscola = $this->contexto()->usar(
            $this->tenantPadrao(),
            fn (): ?RegistroDeAuditoria => RegistroDeAuditoria::query()->find($linha->id),
        );

        $this->assertNull($daEscola);
    }

    #[Test]
    public function uma_escola_nao_ve_a_auditoria_da_plataforma(): void
    {
        // ARRANGE
        $this->contexto()->limparNaTransacao();
        $this->registrar('tenant.desativar');

        // ACT + ASSERT: a escola padrão não enxerga o que o operador fez.
        $this->contexto()->definirNaTransacao($this->tenantPadrao());

        $this->assertSame(0, RegistroDeAuditoria::query()->count());
    }

    #[Test]
    public function a_plataforma_nao_ve_a_auditoria_das_escolas(): void
    {
        // ARRANGE
        $this->registrar('fase.excluir');

        // ACT: o console lê sem contexto.
        $this->contexto()->limparNaTransacao();

        // ASSERT: para ver a auditoria de uma escola, é preciso entrar nela — o mesmo
        // caminho de qualquer outra leitura. Não há atalho.
        $this->assertSame(0, RegistroDeAuditoria::query()->count());

        $vistas = $this->contexto()->usar($this->tenantPadrao(), fn (): int => RegistroDeAuditoria::query()->count());
        $this->assertSame(1, $vistas);
    }

    /**
     * Escrita cruzada é ERRO, e não silêncio. A leitura apenas não devolve nada — a policy
     * filtra; a escrita é recusada pelo `WITH CHECK`, e isso levanta. É a mesma assimetria
     * das 13 tabelas do jogo, e a desejada: ninguém forja uma linha de auditoria em nome de
     * uma escola sem que o log grite.
     */
    #[Test]
    public function o_operador_nao_escreve_no_nome_de_uma_escola(): void
    {
        // ARRANGE: sem contexto, o `WITH CHECK` só aceita `tenant_id` NULL.
        $this->contexto()->limparNaTransacao();

        // ACT + ASSERT
        $this->expectException(QueryException::class);
        $this->expectExceptionMessageMatches('/row-level security policy/');

        DB::table('auditoria')->insert([
            'tenant_id' => $this->tenantPadrao(),
            'acao' => 'forjada', 'alvo_tipo' => 'fase', 'created_at' => now(),
        ]);
    }

    private function tenantPadrao(): int
    {
        return (int) DB::connection('pgsql_dono')->table('tenants')->where('slug', 'padrao')->value('id');
    }
}

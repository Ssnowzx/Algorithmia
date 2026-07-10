<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Dominio\Tenancy\ContextoDoTenant;
use App\Models\Turma;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use LogicException;
use PHPUnit\Framework\Attributes\Test;
use Tests\RefreshDatabase;
use Tests\TestCase;

/**
 * "Nenhum dado de um tenant pode ser acessado, inferido, alterado ou excluído por outro."
 * O roteiro v1 §7 chama a ausência destes testes de bloqueador de produção.
 *
 * A barreira é o RLS do PostgreSQL, e ela só existe porque a aplicação conecta com um
 * papel sem `SUPERUSER` e sem `BYPASSRLS` — ver `TopologiaDeAcessoTest`. Sem aquilo, tudo
 * aqui passaria por acidente.
 *
 * A cobaia é `turmas`, e não uma tabela inventada para o teste: um teste de isolamento
 * sobre algo que o produto não usa envelhece mal.
 *
 * **Tudo é semeado pela conexão do DONO.** A conexão da aplicação está dentro da
 * transação do `RefreshDatabase`: o que ela escreve é invisível ao dono, e as chaves
 * estrangeiras explodiriam. E a aplicação sem contexto não conseguiria inserir nada —
 * que é justamente o que estes testes provam.
 */
final class IsolamentoEntreTenantsTest extends TestCase
{
    use RefreshDatabase;

    private int $escolaA;

    private int $escolaB;

    protected function setUp(): void
    {
        parent::setUp();

        $this->escolaA = $this->criarTenant('Escola A', 'escola-a', 'a.algorithmia.test');
        $this->escolaB = $this->criarTenant('Escola B', 'escola-b', 'b.algorithmia.test');
    }

    protected function tearDown(): void
    {
        // Apagar pela conexão do dono ENQUANTO a transação do teste ainda segura as linhas
        // é um convite ao deadlock: um `UPDATE` que tenha tocado a linha mantém o lock até
        // o rollback, e o `DELETE` do dono espera por ele. Para sempre. Descoberto sabotando
        // o RLS: com a policy ativa, o `UPDATE` cruzado afeta zero linhas e não tranca nada.
        if (DB::transactionLevel() > 0) {
            DB::rollBack();
        }

        $dono = DB::connection('pgsql_dono');
        $escolas = $dono->table('tenants')->where('slug', 'like', 'escola-%')->pluck('id');

        $dono->table('turmas')->whereIn('tenant_id', $escolas)->delete();
        $dono->table('tenant_dominios')->whereIn('tenant_id', $escolas)->delete();
        $dono->table('tenants')->whereIn('id', $escolas)->delete();

        DB::beginTransaction();

        parent::tearDown();
    }

    private function criarTenant(string $nome, string $slug, ?string $host, bool $ativo = true): int
    {
        $dono = DB::connection('pgsql_dono');

        $id = (int) $dono->table('tenants')->insertGetId([
            'nome' => $nome, 'slug' => $slug, 'ativo' => $ativo,
            'created_at' => now(), 'updated_at' => now(),
        ]);

        if ($host !== null) {
            $dono->table('tenant_dominios')->insert([
                'tenant_id' => $id, 'host' => $host, 'primario' => true,
                'created_at' => now(), 'updated_at' => now(),
            ]);
        }

        return $id;
    }

    /** O dono não tem contexto, e o `DEFAULT` de `tenant_id` não o serve: passa-se o id. */
    private function turmaDe(int $tenantId, string $codigo): int
    {
        return (int) DB::connection('pgsql_dono')->table('turmas')->insertGetId([
            'tenant_id' => $tenantId, 'nome' => "Turma {$codigo}", 'codigo' => $codigo, 'ativa' => true,
            'created_at' => now(), 'updated_at' => now(),
        ]);
    }

    private function contexto(): ContextoDoTenant
    {
        return app(ContextoDoTenant::class);
    }

    // ------------------------------------------------------------------ a barreira

    #[Test]
    public function sem_contexto_a_aplicacao_nao_ve_turma_nenhuma(): void
    {
        // ARRANGE
        $this->turmaDe($this->escolaA, 'A1');
        $this->turmaDe($this->escolaB, 'B1');

        // O `TestCase` deixa o contexto no tenant padrão, para que o resto da suíte
        // enxergue o próprio mundo. Aqui queremos a ausência de contexto.
        $this->contexto()->limparNaTransacao();

        // ACT + ASSERT: nenhuma linha, e nenhum erro. A policy filtra, não recusa.
        $this->assertSame(0, Turma::query()->count());
    }

    #[Test]
    public function cada_tenant_ve_apenas_as_suas_turmas(): void
    {
        // ARRANGE
        $this->turmaDe($this->escolaA, 'A1');
        $this->turmaDe($this->escolaA, 'A2');
        $this->turmaDe($this->escolaB, 'B1');

        // ACT + ASSERT
        $this->contexto()->definirNaTransacao($this->escolaA);
        $this->assertSame(2, Turma::query()->count());

        $this->contexto()->definirNaTransacao($this->escolaB);
        $this->assertSame(1, Turma::query()->count());
    }

    #[Test]
    public function uma_query_crua_nao_escapa_da_policy(): void
    {
        // ARRANGE: sem `WHERE`, e sem Eloquent. Filtrar no ORM protege quem usa o ORM.
        $this->turmaDe($this->escolaA, 'A1');
        $this->turmaDe($this->escolaB, 'B1');

        // ACT
        $this->contexto()->definirNaTransacao($this->escolaA);
        $linhas = DB::select('SELECT * FROM turmas');

        // ASSERT
        $this->assertCount(1, $linhas);
        $this->assertSame($this->escolaA, (int) $linhas[0]->tenant_id);
    }

    #[Test]
    public function um_tenant_nao_altera_a_linha_de_outro_nem_conhecendo_o_id(): void
    {
        // ARRANGE
        $idDoOutro = $this->turmaDe($this->escolaB, 'B1');

        // ACT: a escola A tenta renomear a turma da B.
        $this->contexto()->definirNaTransacao($this->escolaA);
        $afetadas = DB::table('turmas')->where('id', $idDoOutro)->update(['nome' => 'Sequestrada']);

        // ASSERT
        $this->assertSame(0, $afetadas);
        $this->assertSame(
            'Turma B1',
            DB::connection('pgsql_dono')->table('turmas')->where('id', $idDoOutro)->value('nome')
        );
    }

    #[Test]
    public function um_tenant_nao_insere_linha_no_nome_de_outro(): void
    {
        // ARRANGE
        $this->contexto()->definirNaTransacao($this->escolaA);

        // ACT + ASSERT: o `WITH CHECK` da policy recusa a escrita, e isso é erro —
        // diferente da leitura, que apenas não devolve nada.
        $this->expectException(QueryException::class);

        DB::table('turmas')->insert([
            'tenant_id' => $this->escolaB,
            'nome' => 'Intrusa', 'codigo' => 'X9', 'ativa' => true,
            'created_at' => now(), 'updated_at' => now(),
        ]);
    }

    // --------------------------------------------------------- o contexto e sua vida

    /**
     * O modo de falha silencioso: o php-fpm reaproveita a conexão, e um `SET` que
     * sobreviva ao pedido entrega os dados da escola A ao pedido seguinte, da B.
     */
    #[Test]
    public function o_contexto_volta_ao_anterior_quando_o_trecho_termina(): void
    {
        // ARRANGE
        $this->turmaDe($this->escolaA, 'A1');
        $this->contexto()->limparNaTransacao();

        // ACT
        $vistas = $this->contexto()->usar($this->escolaA, fn (): int => Turma::query()->count());

        // ASSERT
        $this->assertSame(1, $vistas);
        $this->assertSame(0, Turma::query()->count(), 'o contexto vazou para fora do trecho');
        $this->assertNull($this->contexto()->atual());
    }

    /**
     * O restauro do contexto não pode rodar SQL no caminho de erro.
     *
     * Quando `$trecho()` levanta por causa do PostgreSQL, a transação fica abortada e todo
     * comando seguinte responde 25P02. Um `SET LOCAL` de restauro num `finally` seria esse
     * comando: a sua exceção substituiria a original, e quem investiga leria "current
     * transaction is aborted" no lugar do erro que de fato aconteceu.
     *
     * Isto foi descoberto na Etapa E, depurando um `CHECK` violado que o `usar()` engolia.
     */
    #[Test]
    public function o_erro_original_sobrevive_ao_restauro_do_contexto(): void
    {
        // ARRANGE: uma violação de CHECK deixa a transação do PostgreSQL abortada.
        $this->expectException(QueryException::class);
        $this->expectExceptionMessageMatches('/fases_tipo_check/');

        // ACT + ASSERT
        $this->contexto()->usar($this->escolaA, function (): void {
            DB::table('fases')->insert([
                'ordem_global' => 1, 'nome' => 'Inválida', 'tipo' => 'nao-existe',
            ]);
        });
    }

    #[Test]
    public function o_contexto_em_memoria_volta_ao_anterior_quando_o_trecho_levanta(): void
    {
        // ARRANGE: o `TestCase` deixou o contexto no tenant padrão.
        $antes = $this->contexto()->atual();
        $this->assertNotNull($antes);

        // ACT
        try {
            $this->contexto()->usar($this->escolaA, function (): void {
                throw new LogicException('o trecho falhou');
            });
        } catch (LogicException) {
            // esperado
        }

        // ASSERT: `atual()` não pode ficar apontando para a escola em que o erro ocorreu —
        // o banco já desfez o `SET LOCAL`, e a memória tem de contar a mesma história.
        $this->assertSame($antes, $this->contexto()->atual());
    }

    #[Test]
    public function definir_contexto_fora_de_transacao_e_recusado(): void
    {
        // ARRANGE: `SET LOCAL` fora de transação é um no-op SILENCIOSO no PostgreSQL —
        // o pior modo de falha possível. A aplicação prefere explodir.
        DB::rollBack(); // encerra a transação do RefreshDatabase

        try {
            // ACT + ASSERT
            $this->expectException(LogicException::class);
            $this->contexto()->definirNaTransacao($this->escolaA);
        } finally {
            DB::beginTransaction();
        }
    }

    // ------------------------------------------------------------------ a resolução

    #[Test]
    public function um_host_desconhecido_nao_resolve_tenant_nenhum(): void
    {
        $this->get('http://ninguem.algorithmia.test/')->assertNotFound();
    }

    #[Test]
    public function uma_instituicao_inativa_nao_atende(): void
    {
        // ARRANGE
        $this->criarTenant('Escola C', 'escola-c', 'c.algorithmia.test', ativo: false);

        // ACT + ASSERT
        $this->get('http://c.algorithmia.test/')->assertNotFound();
    }

    #[Test]
    public function o_host_e_comparado_sem_diferenciar_maiusculas(): void
    {
        $this->get('http://A.Algorithmia.TEST/')->assertOk();
    }

    /**
     * `$request->getHost()` só olha `X-Forwarded-Host` se o proxy estiver declarado em
     * `TRUSTED_PROXIES`. Isto amarra o isolamento entre instituições à correção do
     * `RUNBOOK §9`: um `TRUSTED_PROXIES=*` mal posto faria o cliente escolher o tenant.
     */
    #[Test]
    public function um_x_forwarded_host_forjado_nao_escolhe_o_tenant(): void
    {
        // ACT
        $resposta = $this->withHeaders(['X-Forwarded-Host' => 'a.algorithmia.test'])
            ->get('http://ninguem.algorithmia.test/');

        // ASSERT: valeu o Host real. Se o cabeçalho tivesse vencido, seria 200.
        $resposta->assertNotFound();
    }
}

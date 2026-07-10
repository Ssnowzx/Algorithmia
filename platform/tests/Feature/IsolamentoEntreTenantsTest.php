<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Dominio\Tenancy\ContextoDoTenant;
use App\Models\TenantMembro;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use LogicException;
use PHPUnit\Framework\Attributes\Test;
use Tests\RefreshDatabase;
use Tests\TestCase;

/**
 * "Nenhum dado de um tenant pode ser acessado, inferido, alterado ou excluído por
 * outro." O roteiro v1 §7 chama a ausência destes testes de bloqueador de produção.
 *
 * A barreira é o RLS do PostgreSQL, e ela só existe porque a aplicação conecta com um
 * papel sem `SUPERUSER` e sem `BYPASSRLS` — ver `TopologiaDeAcessoTest`. Sem aquilo,
 * tudo aqui passaria por acidente.
 *
 * **Tudo é semeado pela conexão do DONO.** A conexão da aplicação está dentro da
 * transação do `RefreshDatabase`: o que ela escreve é invisível ao dono, e as chaves
 * estrangeiras explodiriam. E, de qualquer modo, a aplicação sem contexto não conseguiria
 * inserir membro nenhum — que é justamente o que estes testes provam.
 */
final class IsolamentoEntreTenantsTest extends TestCase
{
    use RefreshDatabase;

    private int $escolaA;

    private int $escolaB;

    protected function setUp(): void
    {
        parent::setUp();

        config(['tenancy.ativo' => true]);

        $this->escolaA = $this->criarTenant('Escola A', 'escola-a', 'a.algorithmia.test');
        $this->escolaB = $this->criarTenant('Escola B', 'escola-b', 'b.algorithmia.test');
    }

    protected function tearDown(): void
    {
        // O que o dono commitou, o rollback do `RefreshDatabase` não desfaz. Mas apagar
        // pela conexão do dono ENQUANTO a transação do teste ainda segura as linhas é um
        // convite ao deadlock: um `UPDATE` que tenha tocado a linha mantém o lock até o
        // rollback, e o `DELETE` do dono espera por ele. Para sempre.
        //
        // Descoberto sabotando o RLS: com a policy ativa o `UPDATE` afeta zero linhas e
        // não tranca nada — sem ela, a suíte inteira parava. Encerramos a transação do
        // teste primeiro, e reabrimos uma vazia para o `RefreshDatabase` desfazer.
        if (DB::transactionLevel() > 0) {
            DB::rollBack();
        }

        $dono = DB::connection('pgsql_dono');
        $dono->table('tenant_membros')->delete();
        $dono->table('tenant_dominios')->delete();
        $dono->table('tenants')->delete();
        $dono->table('usuarios')->where('email', 'like', '%@escola.test')->delete();

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

    private function membroDe(int $tenantId, string $email): int
    {
        $dono = DB::connection('pgsql_dono');

        $usuarioId = (int) $dono->table('usuarios')->insertGetId([
            'nome' => 'Aluno', 'email' => $email, 'senha_hash' => 'x',
        ]);

        return (int) $dono->table('tenant_membros')->insertGetId([
            'tenant_id' => $tenantId, 'usuario_id' => $usuarioId, 'papel' => 'aluno',
            'created_at' => now(), 'updated_at' => now(),
        ]);
    }

    private function contexto(): ContextoDoTenant
    {
        return app(ContextoDoTenant::class);
    }

    // ------------------------------------------------------------------ a barreira

    #[Test]
    public function sem_contexto_a_aplicacao_nao_ve_membro_nenhum(): void
    {
        // ARRANGE
        $this->membroDe($this->escolaA, 'a@escola.test');
        $this->membroDe($this->escolaB, 'b@escola.test');

        // ACT + ASSERT: nenhuma linha, e nenhum erro. A policy filtra, não recusa.
        $this->assertSame(0, TenantMembro::query()->count());
    }

    #[Test]
    public function cada_tenant_ve_apenas_os_seus_membros(): void
    {
        // ARRANGE
        $this->membroDe($this->escolaA, 'a1@escola.test');
        $this->membroDe($this->escolaA, 'a2@escola.test');
        $this->membroDe($this->escolaB, 'b1@escola.test');

        // ACT + ASSERT
        $this->contexto()->definirNaTransacao($this->escolaA);
        $this->assertSame(2, TenantMembro::query()->count());

        $this->contexto()->definirNaTransacao($this->escolaB);
        $this->assertSame(1, TenantMembro::query()->count());
    }

    #[Test]
    public function uma_query_crua_nao_escapa_da_policy(): void
    {
        // ARRANGE: sem `WHERE`, e sem Eloquent. Filtrar no ORM protege quem usa o ORM.
        $this->membroDe($this->escolaA, 'a@escola.test');
        $this->membroDe($this->escolaB, 'b@escola.test');

        // ACT
        $this->contexto()->definirNaTransacao($this->escolaA);
        $linhas = DB::select('SELECT * FROM tenant_membros');

        // ASSERT
        $this->assertCount(1, $linhas);
        $this->assertSame($this->escolaA, (int) $linhas[0]->tenant_id);
    }

    #[Test]
    public function um_tenant_nao_altera_a_linha_de_outro_nem_conhecendo_o_id(): void
    {
        // ARRANGE
        $idDoOutro = $this->membroDe($this->escolaB, 'b@escola.test');

        // ACT: a escola A tenta promover um membro da B a administrador.
        $this->contexto()->definirNaTransacao($this->escolaA);
        $afetadas = DB::table('tenant_membros')->where('id', $idDoOutro)->update(['papel' => 'tenant_admin']);

        // ASSERT
        $this->assertSame(0, $afetadas);
        $this->assertSame('aluno', DB::connection('pgsql_dono')->table('tenant_membros')->value('papel'));
    }

    #[Test]
    public function um_tenant_nao_insere_linha_no_nome_de_outro(): void
    {
        // ARRANGE
        $usuarioId = (int) DB::connection('pgsql_dono')->table('usuarios')->insertGetId([
            'nome' => 'Intruso', 'email' => 'i@escola.test', 'senha_hash' => 'x',
        ]);
        $this->contexto()->definirNaTransacao($this->escolaA);

        // ACT + ASSERT: o `WITH CHECK` da policy recusa a escrita, e isso é erro —
        // diferente da leitura, que apenas não devolve nada.
        $this->expectException(QueryException::class);

        TenantMembro::create([
            'tenant_id' => $this->escolaB,
            'usuario_id' => $usuarioId,
            'papel' => 'aluno',
        ]);
    }

    // --------------------------------------------------------- o contexto e sua vida

    /**
     * O modo de falha silencioso: o php-fpm reaproveita a conexão, e um `SET` que
     * sobreviva ao pedido entrega os dados da escola A ao pedido seguinte, da B.
     */
    #[Test]
    public function o_contexto_nao_sobrevive_ao_trecho_que_o_definiu(): void
    {
        // ARRANGE
        $this->membroDe($this->escolaA, 'a@escola.test');

        // ACT
        $vistos = $this->contexto()->usar($this->escolaA, fn (): int => TenantMembro::query()->count());

        // ASSERT
        $this->assertSame(1, $vistos);
        $this->assertSame(0, TenantMembro::query()->count(), 'o contexto vazou para fora do trecho');
        $this->assertNull($this->contexto()->atual());
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
        // ACT + ASSERT: 404, e não 500 nem uma página de outra instituição.
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
        // ACT + ASSERT: `Escola.EDU.br` e `escola.edu.br` são o mesmo host.
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
        // ARRANGE: sem proxy confiado, e um host que não existe.
        config(['tenancy.ativo' => true]);

        // ACT
        $resposta = $this->withHeaders(['X-Forwarded-Host' => 'a.algorithmia.test'])
            ->get('http://ninguem.algorithmia.test/');

        // ASSERT: valeu o Host real. Se o cabeçalho tivesse vencido, seria 200.
        $resposta->assertNotFound();
    }
}

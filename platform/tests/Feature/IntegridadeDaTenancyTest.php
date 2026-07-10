<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\Test;
use Tests\RefreshDatabase;
use Tests\TestCase;

/**
 * A sentinela do schema: ela varre o banco e reprova a próxima tabela que nascer torta.
 *
 * Três defeitos desta família chegaram a existir, e nenhum tinha teste — porque cada teste
 * olhava para as tabelas que o autor lembrou de listar:
 *
 * - **`auditoria`** ganhou `tenant_id` só na Etapa E: nasceu antes da tenancy e ficou para
 *   trás, sem RLS;
 * - **`usuarios.email`** tinha índice único GLOBAL. Sob RLS a aplicação não via a linha da
 *   outra escola, tentava inserir, e o índice a denunciava com um 500 numa rota pública;
 * - **`conquistas.codigo`** idem, e por isso `arquivista_do_vazio` só podia existir em uma
 *   instituição no banco inteiro.
 *
 * Este arquivo não lista tabelas. Ele **pergunta ao PostgreSQL** quais são tenant-scoped e
 * exige que todas obedeçam. Uma migration futura que crie uma tabela com `tenant_id` e
 * esqueça o `FORCE ROW LEVEL SECURITY` reprova aqui, sem que ninguém precise lembrar.
 */
final class IntegridadeDaTenancyTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Tabelas que têm a coluna `tenant_id` e **não** são tenant-scoped.
     *
     * `tenant_dominios` é catálogo global, e tem de ser: é a consulta que **descobre** o
     * contexto, e ela roda antes de existir contexto. Uma policy por `app.tenant_id` ali
     * tornaria a resolução impossível — nenhum host resolveria. O `tenant_id` dela é uma
     * chave estrangeira, não um escopo.
     *
     * A lista é curta de propósito. Cada entrada nova aqui é uma exceção à barreira, e tem
     * de doer para escrever.
     *
     * @var array<string,string>
     */
    private const CATALOGO_GLOBAL = [
        'tenant_dominios' => 'o resolvedor a lê antes de existir contexto de tenant',
    ];

    /**
     * Tabelas **sem** `tenant_id`. Cada uma é uma decisão, e a chave é a razão.
     *
     * Esta lista é a que teria pego a `auditoria`: ela nasceu antes da tenancy, ficou sem
     * `tenant_id`, e nenhum teste reclamou — porque todos os testes listavam as tabelas que o
     * autor lembrou. Aqui é o contrário: a tabela nova reprova até alguém escrever por que ela
     * pode ficar de fora.
     *
     * @var array<string,string>
     */
    private const SEM_TENANT_ID = [
        'migrations' => 'infraestrutura do framework',
        'sessions' => 'o StartSession roda ANTES do ResolverTenant; a sessão é amarrada ao tenant no payload, não por RLS',
        'tenants' => 'catálogo global: é a tabela das próprias instituições',
        'operadores' => 'catálogo global: o operador da plataforma não pertence a escola nenhuma',
        'recompensas_batalha' => 'a chave é o UUID da batalha, e ela só é alcançada por personagem_id, que é tenant-scoped',
    ];

    /**
     * Tabelas que **nenhuma migration cria**: cobaias que um teste deixa para trás.
     *
     * `TopologiaDeAcessoTest` cria `ensaio_rls` pela conexão do dono e não a derruba — um
     * `DROP TABLE` esperaria pelo `ACCESS EXCLUSIVE` que a transação do `RefreshDatabase`
     * ainda segura, para sempre. O `migrate:fresh` da execução seguinte a leva junto.
     *
     * Sem esta lista, esta sentinela passaria **por sorte alfabética**: `Integridade…` vem
     * antes de `Topologia…`, e a cobaia ainda não existia. Um teste cuja premissa depende da
     * ordem em que o PHPUnit resolveu rodar não é um teste.
     *
     * @var array<string,string>
     */
    private const COBAIAS_DE_TESTE = [
        'ensaio_rls' => 'criada por TopologiaDeAcessoTest e derrubada pelo migrate:fresh seguinte',
    ];

    /**
     * Índices únicos que NÃO mencionam `tenant_id` e ainda assim estão certos. Cada entrada é
     * uma decisão, e o comentário é a razão dela.
     *
     * A regra geral: um único sobre tabela tenant-scoped precisa incluir `tenant_id`, **ou**
     * ser composto apenas de colunas que apontam para linhas já tenant-scoped — nesse caso a
     * colisão entre escolas é impossível, porque as linhas apontadas nunca se cruzam.
     *
     * @var array<string,string>
     */
    private const UNICOS_JUSTIFICADOS = [
        // Apontam para `personagens`/`usuarios`/`turmas`, que já são tenant-scoped: duas
        // escolas jamais compartilham um personagem, logo jamais colidem aqui.
        'personagens_usuario_id_unique' => 'usuario_id é tenant-scoped',
        'uq_inv' => 'personagem_id e item_id são tenant-scoped',
        'uq_prog' => 'personagem_id e fase_id são tenant-scoped',
        'matriculas_turma_id_usuario_id_unique' => 'turma_id e usuario_id são tenant-scoped',
        'turma_professores_turma_id_usuario_id_unique' => 'turma_id e usuario_id são tenant-scoped',

        // Um token identifica um convite ANTES de se saber de quem ele é. 256 bits de
        // aleatoriedade não colidem, e um único global é o que dá sentido a procurá-lo.
        'convites_token_hash_unique' => 'o token é procurado sem contexto, e é aleatório',
    ];

    /** @return list<string> */
    private function tabelasTenantScoped(): array
    {
        $comColuna = array_map(
            fn (object $l): string => (string) $l->table_name,
            DB::select(
                "SELECT table_name FROM information_schema.columns
                  WHERE table_schema = 'public' AND column_name = 'tenant_id'
                  ORDER BY table_name"
            ),
        );

        return array_values(array_diff(
            $comColuna,
            array_keys(self::CATALOGO_GLOBAL),
            array_keys(self::COBAIAS_DE_TESTE),
        ));
    }

    #[Test]
    public function o_banco_tem_tabelas_tenant_scoped(): void
    {
        // ARRANGE + ACT + ASSERT: uma sentinela que não encontra nada não guarda nada.
        $this->assertGreaterThanOrEqual(17, count($this->tabelasTenantScoped()));
    }

    /**
     * **O teste que teria pego a `auditoria`.** Ela nasceu antes da tenancy e ficou sem
     * `tenant_id`; nenhum teste reclamou, porque todos listavam as tabelas que o autor lembrou.
     */
    #[Test]
    public function toda_tabela_ou_tem_tenant_id_ou_tem_justificativa(): void
    {
        // ARRANGE
        $todas = array_map(
            fn (object $l): string => (string) $l->tablename,
            DB::select("SELECT tablename FROM pg_tables WHERE schemaname = 'public' ORDER BY tablename"),
        );

        $comEscopo = array_merge(
            $this->tabelasTenantScoped(),
            array_keys(self::CATALOGO_GLOBAL),
            array_keys(self::SEM_TENANT_ID),
            array_keys(self::COBAIAS_DE_TESTE),
        );

        // ACT
        $orfas = array_values(array_diff($todas, $comEscopo));

        // ASSERT
        $this->assertSame(
            [],
            $orfas,
            'tabela sem `tenant_id`. Ou lhe dê a coluna e o RLS, ou justifique-a em '
            .'IntegridadeDaTenancyTest::SEM_TENANT_ID.',
        );
    }

    /** A justificativa de ficar sem `tenant_id` também não pode virar folclore. */
    #[Test]
    public function nenhuma_tabela_justificada_ganhou_tenant_id_as_escondidas(): void
    {
        foreach (array_keys(self::SEM_TENANT_ID) as $tabela) {
            $coluna = DB::selectOne(
                "SELECT 1 AS existe FROM information_schema.columns
                  WHERE table_schema = 'public' AND table_name = ? AND column_name = 'tenant_id'",
                [$tabela],
            );

            $this->assertNull($coluna, "`{$tabela}` ganhou `tenant_id`: tire-a da lista de exceções");
        }
    }

    #[Test]
    public function toda_tabela_com_tenant_id_tem_rls_ligado_e_forcado(): void
    {
        // ARRANGE + ACT
        $sem = [];

        foreach ($this->tabelasTenantScoped() as $tabela) {
            $classe = DB::selectOne(
                "SELECT relrowsecurity AS rls, relforcerowsecurity AS forcado
                   FROM pg_class WHERE relname = ? AND relnamespace = 'public'::regnamespace",
                [$tabela],
            );

            $this->assertNotNull($classe, "a tabela `{$tabela}` sumiu do pg_class");

            // `FORCE` é o que faz a policy valer também para o dono das tabelas. Sem ele, a
            // barreira existe para todos menos para quem tem mais poder de atravessá-la.
            if ((bool) $classe->rls !== true || (bool) $classe->forcado !== true) {
                $sem[] = $tabela;
            }
        }

        // ASSERT
        $this->assertSame([], $sem, 'tabelas tenant-scoped sem ENABLE + FORCE ROW LEVEL SECURITY');
    }

    #[Test]
    public function toda_tabela_com_tenant_id_tem_policy(): void
    {
        // ARRANGE + ACT
        $sem = [];

        foreach ($this->tabelasTenantScoped() as $tabela) {
            $politicas = DB::selectOne('SELECT count(*) AS total FROM pg_policies WHERE tablename = ?', [$tabela]);

            $this->assertNotNull($politicas);

            if ((int) $politicas->total === 0) {
                $sem[] = $tabela;
            }
        }

        // ASSERT: RLS ligado sem policy nenhuma esconde tudo de todos — inclusive do dono.
        $this->assertSame([], $sem, 'tabelas com RLS ligado e nenhuma policy');
    }

    #[Test]
    public function toda_tabela_com_tenant_id_referencia_tenants(): void
    {
        // ARRANGE + ACT: sem a chave estrangeira, um `tenant_id` órfão aponta para o vazio, e
        // a linha pertence a todo mundo e a ninguém.
        $sem = [];

        foreach ($this->tabelasTenantScoped() as $tabela) {
            $fk = DB::selectOne(
                "SELECT 1 AS existe FROM pg_constraint
                  WHERE conrelid = ?::regclass AND contype = 'f'
                    AND confrelid = 'tenants'::regclass",
                [$tabela],
            );

            if ($fk === null) {
                $sem[] = $tabela;
            }
        }

        // ASSERT
        $this->assertSame([], $sem, 'tabelas tenant-scoped sem FK para tenants');
    }

    /**
     * **O teste que teria pego `usuarios.email` e `conquistas.codigo`.**
     *
     * Um índice único global sobre tabela tenant-scoped é a mesma classe de defeito da chave
     * primária global: o RLS esconde a linha da outra escola, e o índice a denuncia. O melhor
     * caso é uma segunda instituição que não pode existir; o pior é um oráculo de existência
     * numa rota pública, com 500 no lugar de um erro de validação.
     */
    #[Test]
    public function nenhum_indice_unico_ignora_o_tenant_id_sem_justificativa(): void
    {
        // ARRANGE
        $tabelas = $this->tabelasTenantScoped();

        $indices = DB::select(
            "SELECT tablename, indexname, indexdef
               FROM pg_indexes
              WHERE schemaname = 'public'
                AND indexdef LIKE '%UNIQUE%'
                AND indexname NOT LIKE '%\\_pkey'
              ORDER BY tablename, indexname"
        );

        // ACT
        $suspeitos = [];

        foreach ($indices as $indice) {
            if (! in_array($indice->tablename, $tabelas, true)) {
                continue; // catálogo global: `tenants`, `tenant_dominios`, `operadores`
            }

            if (str_contains($indice->indexdef, 'tenant_id')) {
                continue;
            }

            if (array_key_exists($indice->indexname, self::UNICOS_JUSTIFICADOS)) {
                continue;
            }

            $suspeitos[] = $indice->tablename.'.'.$indice->indexname;
        }

        // ASSERT
        $this->assertSame(
            [],
            $suspeitos,
            'índice único global sobre tabela tenant-scoped. Ou inclua `tenant_id`, ou '
            .'justifique-o em IntegridadeDaTenancyTest::UNICOS_JUSTIFICADOS.',
        );
    }

    /**
     * A justificativa não pode envelhecer em silêncio: se o índice sumir ou for corrigido, a
     * entrada tem de sair da lista, ou ela vira folclore.
     */
    #[Test]
    public function toda_justificativa_corresponde_a_um_indice_que_ainda_existe(): void
    {
        // ARRANGE + ACT
        $existentes = array_map(
            fn (object $l): string => (string) $l->indexname,
            DB::select("SELECT indexname FROM pg_indexes WHERE schemaname = 'public'"),
        );

        // ASSERT
        foreach (array_keys(self::UNICOS_JUSTIFICADOS) as $indice) {
            $this->assertContains($indice, $existentes, "a justificativa de `{$indice}` sobreviveu ao índice");
        }
    }

    /** A exceção do catálogo global também não pode virar folclore. */
    #[Test]
    public function o_catalogo_global_ainda_existe_e_continua_sem_rls(): void
    {
        foreach (array_keys(self::CATALOGO_GLOBAL) as $tabela) {
            $classe = DB::selectOne(
                "SELECT relrowsecurity AS rls FROM pg_class
                  WHERE relname = ? AND relnamespace = 'public'::regnamespace",
                [$tabela],
            );

            $this->assertNotNull($classe, "a tabela `{$tabela}` sumiu, e a exceção ficou");

            // Se um dia ela ganhar RLS, o `ResolverTenant` para de resolver — e este teste
            // avisa antes de a produção avisar.
            $this->assertFalse((bool) $classe->rls, "`{$tabela}` ganhou RLS: o resolvedor não consegue mais ler o host");
        }
    }
}

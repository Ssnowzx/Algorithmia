<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Dominio\Tenancy\ContextoDoTenant;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\RefreshDatabase;
use Tests\TestCase;

/**
 * Etapas C.1 e C.2: a coluna existe, é obrigatória, e manda.
 *
 * O que torna a C.2 barata é o `DEFAULT` da coluna: ele lê `app.tenant_id`, e uma linha
 * nova herda o tenant da requisição que a criou. Nenhuma linha do código de inserção do
 * jogo mudou. O que a torna segura é o `NOT NULL`: sem contexto, a inserção falha alto.
 */
final class TenantIdNoJogoTest extends TestCase
{
    use RefreshDatabase;

    /** As 13 do importador. */
    private const TABELAS = [
        'usuarios', 'mestres', 'itens', 'conquistas', 'personagens', 'fases', 'desafios',
        'dialogos', 'inventario', 'progresso_fases', 'conquistas_personagem', 'escolhas',
        'respostas_log',
    ];

    #[Test]
    public function as_treze_tabelas_do_jogo_ganharam_a_coluna(): void
    {
        foreach (self::TABELAS as $tabela) {
            // ASSERT
            $this->assertTrue(
                Schema::hasColumn($tabela, 'tenant_id'),
                "{$tabela} ficou sem tenant_id — o importador a copia, o RLS vai cobri-la"
            );
        }
    }

    #[Test]
    #[TestWith(['fases'])]
    #[TestWith(['desafios'])]
    #[TestWith(['respostas_log'])]
    public function cada_coluna_tem_indice(string $tabela): void
    {
        // ARRANGE + ACT: sem índice, toda consulta filtrada por tenant vira varredura.
        $indices = DB::select(
            'SELECT indexname FROM pg_indexes WHERE tablename = ? AND indexname = ?',
            [$tabela, "{$tabela}_tenant_id_idx"]
        );

        // ASSERT
        $this->assertCount(1, $indices);
    }

    #[Test]
    public function existe_um_tenant_padrao_com_o_host_do_app_url(): void
    {
        // ARRANGE
        $host = parse_url((string) config('app.url'), PHP_URL_HOST);

        // ACT
        $tenant = DB::table('tenants')->where('slug', 'padrao')->first();

        // ASSERT
        $this->assertNotNull($tenant, 'sem tenant padrão, o SET NOT NULL não tem para onde apontar');
        $this->assertTrue((bool) $tenant->ativo);
        $this->assertSame(1, DB::table('tenant_dominios')
            ->where('tenant_id', $tenant->id)->where('host', $host)->count());
    }

    /** C.2: a coluna deixou de ser anulável. Sem backfill, este `ALTER` teria falhado. */
    #[Test]
    #[TestWith(['fases'])]
    #[TestWith(['usuarios'])]
    #[TestWith(['respostas_log'])]
    public function a_coluna_e_obrigatoria(string $tabela): void
    {
        // ARRANGE + ACT
        $coluna = DB::selectOne(
            'SELECT is_nullable FROM information_schema.columns
              WHERE table_name = ? AND column_name = ?',
            [$tabela, 'tenant_id']
        );

        // ASSERT
        $this->assertSame('NO', $coluna->is_nullable);
    }

    /**
     * O que torna a C.2 barata: nenhuma linha do código de inserção mudou. O `DEFAULT`
     * da coluna lê `app.tenant_id`, e uma linha nova herda o tenant da requisição que a
     * criou. É o `TestCase` quem define esse contexto aqui.
     */
    #[Test]
    public function uma_linha_nova_herda_o_tenant_do_contexto_sem_ninguem_passar_o_id(): void
    {
        // ARRANGE
        $padrao = (int) DB::connection('pgsql_dono')->table('tenants')->where('slug', 'padrao')->value('id');

        // ACT: repare que `tenant_id` não aparece.
        $id = DB::table('mestres')->insertGetId([
            'nome' => 'Herdeiro', 'titulo' => 't', 'disciplina' => 'd',
            'regiao' => 'r', 'svg_slug' => 's', 'ordem' => 99,
        ]);

        // ASSERT
        $this->assertSame($padrao, (int) DB::table('mestres')->where('id', $id)->value('tenant_id'));
    }

    /**
     * E o que a torna segura: sem contexto, a inserção **falha**. Uma linha órfã
     * pertenceria a todo mundo e a ninguém — falhar alto é o comportamento desejado.
     */
    #[Test]
    public function sem_contexto_nenhuma_linha_nova_entra(): void
    {
        // ARRANGE
        app(ContextoDoTenant::class)->limparNaTransacao();

        // ACT + ASSERT
        $this->expectException(QueryException::class);

        DB::table('mestres')->insert([
            'nome' => 'Órfão', 'titulo' => 't', 'disciplina' => 'd',
            'regiao' => 'r', 'svg_slug' => 's', 'ordem' => 99,
        ]);
    }

    #[Test]
    #[TestWith(['fases'])]
    #[TestWith(['desafios'])]
    #[TestWith(['usuarios'])]
    public function a_tabela_tem_rls_ligado_e_forcado(string $tabela): void
    {
        // ARRANGE + ACT: `FORCE` é o que faz a policy valer também para o dono.
        $classe = DB::selectOne(
            'SELECT relrowsecurity AS rls, relforcerowsecurity AS forcado
               FROM pg_class WHERE relname = ? AND relnamespace = \'public\'::regnamespace',
            [$tabela]
        );

        // ASSERT
        $this->assertTrue((bool) $classe->rls);
        $this->assertTrue((bool) $classe->forcado);
    }
}

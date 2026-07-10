<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\RefreshDatabase;
use Tests\TestCase;

/**
 * Etapa C.1: a coluna existe e está preenchida, mas ainda não manda. O `SET NOT NULL` e
 * as policies vêm na migration seguinte — a fronteira é deliberada, e estes testes a
 * travam nos dois lados.
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

    /**
     * A coluna ainda é anulável de propósito. Quem a tornar obrigatória sem backfill
     * quebra o deploy no meio, com metade do schema aplicado — este teste é o aviso.
     */
    #[Test]
    public function a_coluna_ainda_e_anulavel_e_o_backfill_e_quem_a_preenche(): void
    {
        // ARRANGE + ACT
        $coluna = DB::selectOne(
            "SELECT is_nullable FROM information_schema.columns
              WHERE table_name = 'fases' AND column_name = 'tenant_id'"
        );

        // ASSERT
        $this->assertSame('YES', $coluna->is_nullable);
    }

    /**
     * A lacuna que a Etapa C.2 fecha, escrita como teste para que ninguém a esqueça.
     *
     * Hoje o jogo insere linhas sem `tenant_id`, e o banco aceita. Nada as protege: uma
     * `fase` órfã pertence a todo mundo e a ninguém. Quando o `NOT NULL` e as policies
     * entrarem, este teste passa a falhar — e essa falha é a definição de pronto.
     */
    #[Test]
    public function hoje_ainda_se_insere_linha_sem_tenant_e_e_exatamente_isso_que_falta(): void
    {
        // ARRANGE + ACT
        $id = DB::table('mestres')->insertGetId([
            'nome' => 'Órfão', 'titulo' => 't', 'disciplina' => 'd',
            'regiao' => 'r', 'svg_slug' => 's', 'ordem' => 99,
        ]);

        // ASSERT
        $this->assertNull(DB::table('mestres')->where('id', $id)->value('tenant_id'));
    }
}

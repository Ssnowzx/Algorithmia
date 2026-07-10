<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Etapa C.2: `tenant_id` deixa de ser uma coluna e passa a ser a lei.
 *
 * Três coisas, e a ordem entre elas importa:
 *
 * 1. **`DEFAULT` que lê o contexto.** `NULLIF(current_setting('app.tenant_id', true), '')::bigint`.
 *    É o que permite que nenhuma linha de código de inserção mude: uma linha nova herda o
 *    tenant da requisição que a criou. Sem contexto, o default é NULL — e o `NOT NULL`
 *    abaixo derruba a inserção. Falhar alto é o comportamento desejado: uma linha órfã
 *    pertenceria a todo mundo e a ninguém.
 *
 * 2. **`NOT NULL`.** Só é possível porque a C.1 preencheu tudo. Se alguma linha tivesse
 *    escapado, este `ALTER` falharia no meio do deploy, com metade do schema aplicado.
 *
 * 3. **RLS + `FORCE` + policy.** A partir daqui, uma consulta sem contexto devolve zero
 *    linhas — e não erro. A policy filtra, não recusa. É o que faz o vazamento entre
 *    instituições ser impossível, e não apenas improvável.
 *
 * **`usuarios` fica tenant-scoped, e isso diverge do roteiro v1 §5**, que os queria
 * globais, ligados às instituições por `tenant_memberships`. Divergimos de propósito: o
 * índice único de e-mail é global (`lower(email)`), o jogo não tem ninguém em duas
 * escolas, e tornar a conta global exige resolver a identidade no login antes de saber o
 * tenant — trabalho de verdade, sem demanda. Fica registrado como pergunta aberta da
 * Etapa D. A consequência prática, hoje: **duas escolas não podem ter o mesmo e-mail.**
 *
 * `recompensas_batalha` não entra: sua chave é o UUID da batalha, e ela é alcançada
 * apenas por `personagem_id`, que já é tenant-scoped.
 */
return new class extends Migration
{
    /** As 13 do importador — as mesmas que a C.1 tocou. */
    private const TABELAS = [
        'usuarios', 'mestres', 'itens', 'conquistas', 'personagens', 'fases', 'desafios',
        'dialogos', 'inventario', 'progresso_fases', 'conquistas_personagem', 'escolhas',
        'respostas_log',
    ];

    public function up(): void
    {
        $variavel = (string) config('tenancy.variavel_de_sessao');
        $expressao = sprintf("NULLIF(current_setting('%s', true), '')::bigint", $variavel);

        foreach (self::TABELAS as $tabela) {
            DB::statement("ALTER TABLE {$tabela} ALTER COLUMN tenant_id SET DEFAULT {$expressao}");
            DB::statement("ALTER TABLE {$tabela} ALTER COLUMN tenant_id SET NOT NULL");

            // `RESTRICT`, e não `CASCADE`: apagar um tenant não pode apagar em silêncio o
            // progresso de uma escola inteira. Quem quiser removê-la que esvazie antes, e
            // saiba o que está fazendo.
            DB::statement(
                "ALTER TABLE {$tabela}
                   ADD CONSTRAINT {$tabela}_tenant_id_fk
                   FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE RESTRICT"
            );

            DB::statement("ALTER TABLE {$tabela} ENABLE ROW LEVEL SECURITY");
            DB::statement("ALTER TABLE {$tabela} FORCE ROW LEVEL SECURITY");

            DB::statement(
                "CREATE POLICY {$tabela}_por_tenant ON {$tabela}
                     USING (tenant_id = {$expressao})
                     WITH CHECK (tenant_id = {$expressao})"
            );
        }
    }

    public function down(): void
    {
        foreach (self::TABELAS as $tabela) {
            DB::statement("DROP POLICY IF EXISTS {$tabela}_por_tenant ON {$tabela}");
            DB::statement("ALTER TABLE {$tabela} NO FORCE ROW LEVEL SECURITY");
            DB::statement("ALTER TABLE {$tabela} DISABLE ROW LEVEL SECURITY");
            DB::statement("ALTER TABLE {$tabela} DROP CONSTRAINT IF EXISTS {$tabela}_tenant_id_fk");
            DB::statement("ALTER TABLE {$tabela} ALTER COLUMN tenant_id DROP NOT NULL");
            DB::statement("ALTER TABLE {$tabela} ALTER COLUMN tenant_id DROP DEFAULT");
        }
    }
};

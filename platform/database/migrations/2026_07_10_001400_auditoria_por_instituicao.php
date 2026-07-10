<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * `auditoria` passa a pertencer a uma instituição — ou a nenhuma.
 *
 * Ela nasceu antes da tenancy e ficou para trás: sem `tenant_id`, sem RLS. Não vazava,
 * porque nenhuma rota a lê — ela só é escrita. Mas as linhas de duas escolas conviviam numa
 * tabela sem barreira, e a primeira tela que as mostrasse as misturaria. Uma trilha de
 * auditoria que não sabe de quem é não é uma trilha de auditoria.
 *
 * **O problema que adiou este conserto, e como ele se resolve.** Há dois tipos de autor, e
 * eles vivem em mundos diferentes:
 *
 * - o **usuário** de uma escola age dentro de um contexto de tenant;
 * - o **operador** da plataforma age fora de qualquer contexto — ele não pertence a escola
 *   nenhuma, e o `/console` roda sem `ResolverTenant`.
 *
 * Uma policy `tenant_id = current_setting(...)` recusaria a escrita do operador (`NULL = NULL`
 * é NULL, e NULL não é `true`). E uma que deixasse as linhas dele visíveis a todos os tenants
 * seria pior do que a ausência de barreira.
 *
 * A resposta é `IS NOT DISTINCT FROM`, que trata NULL como um valor:
 *
 * | quem escreve | contexto | `tenant_id` gravado | quem enxerga |
 * |---|---|---|---|
 * | aluno/professor/mestre | a escola dele | o id da escola | só aquela escola |
 * | operador da plataforma | nenhum | `NULL` | só quem estiver sem contexto (o console) |
 *
 * Ou seja: **uma escola nunca vê a auditoria de outra, nem a da plataforma; e o console
 * nunca vê a auditoria das escolas sem entrar em uma delas.** É a mesma regra do resto do
 * sistema, com o operador ocupando a única posição que sobrava.
 *
 * A coluna fica **anulável** de propósito: `NULL` aqui significa "a plataforma", e não
 * "esqueceram de preencher".
 *
 * Aditiva: coluna nova e anulável, com DEFAULT. O código velho — que não menciona
 * `tenant_id` — continua gravando, e a linha herda o contexto da requisição.
 */
return new class extends Migration
{
    public function up(): void
    {
        $variavel = (string) config('tenancy.variavel_de_sessao');
        $contexto = sprintf("NULLIF(current_setting('%s', true), '')::bigint", $variavel);

        DB::statement("ALTER TABLE auditoria ADD COLUMN tenant_id BIGINT NULL DEFAULT {$contexto}");

        // As linhas que já existem foram escritas por mestres, na era de uma instituição só:
        // o console não existia, e o operador tampouco. Elas pertencem à instituição padrão.
        $padrao = DB::table('tenants')->orderBy('id')->value('id');

        if ($padrao !== null) {
            DB::table('auditoria')->whereNull('tenant_id')->update(['tenant_id' => $padrao]);
        }

        Schema::table('auditoria', function (Illuminate\Database\Schema\Blueprint $table): void {
            $table->index('tenant_id', 'auditoria_tenant_id_idx');
        });

        // `RESTRICT`, como nas 13 do jogo: apagar uma instituição não pode apagar em silêncio
        // o registro do que foi feito nela.
        DB::statement(
            'ALTER TABLE auditoria
               ADD CONSTRAINT auditoria_tenant_id_fk
               FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE RESTRICT'
        );

        DB::statement('ALTER TABLE auditoria ENABLE ROW LEVEL SECURITY');
        DB::statement('ALTER TABLE auditoria FORCE ROW LEVEL SECURITY');

        // `IS NOT DISTINCT FROM` e não `=`: sem contexto, os dois lados são NULL, e `NULL = NULL`
        // é NULL — o que reprova tanto a leitura quanto a escrita do operador. Com `IS NOT
        // DISTINCT FROM`, NULL casa com NULL, e a linha da plataforma existe para a plataforma.
        DB::statement(
            "CREATE POLICY auditoria_por_tenant ON auditoria
                 USING (tenant_id IS NOT DISTINCT FROM {$contexto})
                 WITH CHECK (tenant_id IS NOT DISTINCT FROM {$contexto})"
        );
    }

    public function down(): void
    {
        DB::statement('DROP POLICY IF EXISTS auditoria_por_tenant ON auditoria');
        DB::statement('ALTER TABLE auditoria NO FORCE ROW LEVEL SECURITY');
        DB::statement('ALTER TABLE auditoria DISABLE ROW LEVEL SECURITY');
        DB::statement('ALTER TABLE auditoria DROP CONSTRAINT IF EXISTS auditoria_tenant_id_fk');

        Schema::table('auditoria', function (Illuminate\Database\Schema\Blueprint $table): void {
            $table->dropIndex('auditoria_tenant_id_idx');
            $table->dropColumn('tenant_id');
        });
    }
};

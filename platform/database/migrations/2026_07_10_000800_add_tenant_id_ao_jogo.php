<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * `tenant_id` nas 13 tabelas do jogo, e o tenant padrão.
 *
 * A coluna nasce **anulável**, e é preenchida aqui mesmo. O `SET NOT NULL` e as policies
 * de RLS vêm na migration seguinte, de propósito: quem lê este arquivo consegue ver a
 * fronteira entre "a coluna existe" e "a coluna manda".
 *
 * **Por que isto acontece antes do corte, e não depois.** A regra dos três deploys é uma
 * regra de coexistência: migration roda contra o código velho ainda no ar. Antes do
 * corte, o port nunca esteve em produção — não há código velho. E o rollback do corte é
 * trocar o DNS de volta para o legado, cujo MySQL está intacto: não há schema a desfazer.
 * Depois do corte, a mesma mudança seria irreversível sobre progresso real de alunos.
 *
 * O tenant padrão é criado a partir de `APP_URL` quando não existe nenhum. É dado dentro
 * de migration, e isso pede justificativa: sem ele, a migration seguinte não tem para
 * onde apontar as linhas existentes, e um banco populado ficaria com `tenant_id` nulo em
 * 1.306 linhas — que é exatamente o estado que o `NOT NULL` recusa.
 */
return new class extends Migration
{
    /** As 13 do importador. `sessions`, `migrations` e a tenancy ficam de fora. */
    private const TABELAS = [
        'usuarios', 'mestres', 'itens', 'conquistas', 'personagens', 'fases', 'desafios',
        'dialogos', 'inventario', 'progresso_fases', 'conquistas_personagem', 'escolhas',
        'respostas_log',
    ];

    public function up(): void
    {
        foreach (self::TABELAS as $tabela) {
            // Sem `after()`: o PostgreSQL não posiciona colunas, e o modificador seria
            // ignorado em silêncio — dando a impressão de que a ordem foi respeitada.
            Schema::table($tabela, function (Blueprint $t) use ($tabela): void {
                $t->unsignedBigInteger('tenant_id')->nullable();
                $t->index('tenant_id', "{$tabela}_tenant_id_idx");
            });
        }

        $tenantId = $this->tenantPadrao();

        // Backfill. Sem ele, o `SET NOT NULL` da migration seguinte encontraria 1.306
        // linhas nulas — e falharia no meio do deploy, com metade do schema aplicado.
        foreach (self::TABELAS as $tabela) {
            DB::table($tabela)->whereNull('tenant_id')->update(['tenant_id' => $tenantId]);
        }
    }

    public function down(): void
    {
        foreach (self::TABELAS as $tabela) {
            Schema::table($tabela, function (Blueprint $t) use ($tabela): void {
                $t->dropIndex("{$tabela}_tenant_id_idx");
                $t->dropColumn('tenant_id');
            });
        }
    }

    /**
     * O tenant que herda tudo o que já existe. Idempotente: num banco que já o tenha,
     * devolve o que está lá, e não cria um segundo.
     */
    private function tenantPadrao(): int
    {
        $existente = DB::table('tenants')->orderBy('id')->value('id');

        if ($existente !== null) {
            return (int) $existente;
        }

        $host = parse_url((string) config('app.url'), PHP_URL_HOST) ?: 'localhost';

        $id = (int) DB::table('tenants')->insertGetId([
            'nome' => (string) config('app.name'),
            'slug' => 'padrao',
            'ativo' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('tenant_dominios')->insert([
            'tenant_id' => $id,
            'host' => mb_strtolower($host),
            'primario' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return $id;
    }
};

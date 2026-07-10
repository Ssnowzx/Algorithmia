<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Etapa E.1: liberação progressiva de funcionalidades, por instituição.
 *
 * **O banco guarda apenas as exceções.** O catálogo de flags — quais existem, o que cada
 * uma faz, e o valor padrão — mora em `config/flags.php`, versionado. Aqui fica só o que
 * uma instituição decidiu diferente do padrão.
 *
 * Isso resolve o problema que uma tabela `tenant_flags` não resolve: uma flag nova nasce
 * valendo para todas as escolas, sem backfill, e apagá-la do código a apaga de todas as
 * instituições de uma vez. Uma linha por (tenant, flag) exigiria uma migration de dados
 * a cada flag criada, e deixaria órfãs a cada flag removida.
 *
 * `tenants` é catálogo global, sem RLS — o resolvedor a lê antes de existir contexto. As
 * flags viajam junto com o tenant que o `ResolverTenant` já carregou: **nenhuma consulta
 * a mais por requisição**.
 *
 * Aditiva: coluna nova, com DEFAULT. As instituições existentes passam a ter `{}`, que
 * significa "tudo no padrão do código" — exatamente o comportamento de antes desta
 * migration.
 */
return new class extends Migration
{
    public function up(): void
    {
        // `NOT NULL DEFAULT '{}'` e não anulável: um JSON nulo e um JSON vazio diriam a
        // mesma coisa, e todo leitor teria de tratar os dois casos. Um estado a menos.
        DB::statement("ALTER TABLE tenants ADD COLUMN flags JSONB NOT NULL DEFAULT '{}'::jsonb");
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE tenants DROP COLUMN flags');
    }
};

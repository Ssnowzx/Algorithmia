<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * A fundação de tenancy. Aditiva: tabelas novas, nada tocado.
 *
 * **Nem toda tabela pode ser tenant-scoped, e isso não é descuido.** O resolvedor lê
 * `tenant_dominios` para descobrir QUAL é o tenant — antes, portanto, de existir
 * contexto. Uma policy por `app.tenant_id` ali tornaria a resolução impossível: a
 * primeira consulta da requisição não veria linha nenhuma, e nenhum host resolveria.
 *
 * Então:
 *
 * - `tenants` e `tenant_dominios` são **catálogo global**, sem RLS. O que elas expõem
 *   é o nome e o domínio de instituições — dados que já estão no DNS de quem as visita.
 * - `tenant_membros` e `convites` guardam quem pertence a quê, com que papel, e são
 *   **tenant-scoped**, com `FORCE ROW LEVEL SECURITY`.
 *
 * O `FORCE` é cinto e suspensório: se um dia alguém conectar a aplicação com o dono por
 * engano, as policies continuam valendo. Sem ele, o dono passa direto.
 */
return new class extends Migration
{
    /** As tabelas tenant-scoped. `tenants`/`tenant_dominios` são catálogo global. */
    private const COM_RLS = ['tenant_membros', 'convites'];

    public function up(): void
    {
        Schema::create('tenants', function (Blueprint $table): void {
            $table->id();
            $table->string('nome', 150);
            $table->string('slug', 80)->unique();
            $table->boolean('ativo')->default(true);
            $table->timestamps();
        });

        Schema::create('tenant_dominios', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('host', 253);
            $table->boolean('primario')->default(false);
            $table->timestamps();
        });

        // Host é comparado em minúsculas, sempre: `Escola.EDU.br` e `escola.edu.br` são
        // o mesmo host, e um índice `unique` sobre a coluna crua deixaria os dois entrar.
        DB::statement('CREATE UNIQUE INDEX tenant_dominios_host_unico ON tenant_dominios (lower(host))');

        Schema::create('tenant_membros', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('usuario_id')->constrained('usuarios')->cascadeOnDelete();
            $table->string('papel', 30);
            $table->timestamps();

            $table->unique(['tenant_id', 'usuario_id']);
            $table->index('tenant_id');
        });

        Schema::create('convites', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('email', 255);
            $table->string('papel', 30);

            // O token nunca é guardado em claro: quem lê o banco não pode aceitar o
            // convite de ninguém.
            $table->string('token_hash', 64)->unique();

            $table->timestamp('expira_em');
            $table->timestamp('aceito_em')->nullable();
            $table->timestamps();

            $table->index('tenant_id');
        });

        $variavel = (string) config('tenancy.variavel_de_sessao');

        foreach (self::COM_RLS as $tabela) {
            DB::statement("ALTER TABLE {$tabela} ENABLE ROW LEVEL SECURITY");
            DB::statement("ALTER TABLE {$tabela} FORCE ROW LEVEL SECURITY");

            // `current_setting(…, true)` devolve NULL quando não há contexto — e NULL não
            // casa com `tenant_id` nenhum. Ausência de contexto vira "nenhuma linha", e
            // não "todas as linhas". Sem o `true`, seria um erro, e o operador aprenderia
            // a contorná-lo.
            //
            // `NULLIF(…, '')` porque `''::bigint` é erro, e um `SET` mal feito não deve
            // derrubar a requisição — deve não ver nada.
            DB::statement(sprintf(
                "CREATE POLICY %s_por_tenant ON %s
                     USING (tenant_id = NULLIF(current_setting('%s', true), '')::bigint)
                     WITH CHECK (tenant_id = NULLIF(current_setting('%s', true), '')::bigint)",
                $tabela, $tabela, $variavel, $variavel
            ));
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('convites');
        Schema::dropIfExists('tenant_membros');
        Schema::dropIfExists('tenant_dominios');
        Schema::dropIfExists('tenants');
    }
};

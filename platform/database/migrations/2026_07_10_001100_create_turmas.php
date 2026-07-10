<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Turmas, matrículas e o vínculo professor–turma. Etapa D do roteiro v1.
 *
 * **O papel do professor entra em `usuarios.papel`, e não numa tabela de membership.**
 * A Etapa C tornou `usuarios` tenant-scoped: uma conta pertence a exatamente uma
 * instituição. Nesse mundo, `tenant_membros` é 1:1 com o usuário — uma tabela redundante
 * e, pior, uma segunda fonte de verdade para o papel. Ela é removida aqui.
 *
 * `convites` fica: convidar um professor por e-mail continua sendo uma coisa que existe.
 *
 * Fica registrado que isto **diverge do roteiro v1 §5**, que queria contas globais
 * ligadas às instituições por `tenant_memberships`. A divergência tem preço: duas escolas
 * não podem ter o mesmo e-mail. Reverter exige resolver a identidade no login antes de
 * saber o tenant — trabalho de verdade, e sem demanda.
 *
 * **Tenancy vs. autorização são coisas diferentes, e as duas são necessárias.** O RLS
 * garante que uma escola não vê a outra. Ele **não** garante que um professor só vê as
 * suas turmas: isso é autorização, mora nas policies do Laravel, e tem testes próprios.
 * Confundir as duas é como trancar o prédio e deixar as salas abertas.
 */
return new class extends Migration
{
    private const TENANT_SCOPED = ['turmas', 'matriculas', 'turma_professores'];

    public function up(): void
    {
        $variavel = (string) config('tenancy.variavel_de_sessao');
        $contexto = sprintf("NULLIF(current_setting('%s', true), '')::bigint", $variavel);

        Schema::create('turmas', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->string('nome', 120);

            // O código que o aluno digita para entrar. Único por instituição, e não
            // globalmente: duas escolas podem ter a turma "9A".
            $table->string('codigo', 20);
            $table->boolean('ativa')->default(true);
            $table->timestamps();

            $table->unique(['tenant_id', 'codigo']);
            $table->index('tenant_id');
        });

        Schema::create('matriculas', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->foreignId('turma_id')->constrained('turmas')->cascadeOnDelete();
            $table->foreignId('usuario_id')->constrained('usuarios')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['turma_id', 'usuario_id']);
            $table->index('tenant_id');
        });

        Schema::create('turma_professores', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->foreignId('turma_id')->constrained('turmas')->cascadeOnDelete();
            $table->foreignId('usuario_id')->constrained('usuarios')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['turma_id', 'usuario_id']);
            $table->index('tenant_id');
        });

        foreach (self::TENANT_SCOPED as $tabela) {
            // O mesmo desenho da Etapa C: o DEFAULT lê o contexto, o NOT NULL exige que
            // ele exista, e a policy filtra. Nenhuma linha nova precisa passar `tenant_id`.
            DB::statement("ALTER TABLE {$tabela} ALTER COLUMN tenant_id SET DEFAULT {$contexto}");
            DB::statement("ALTER TABLE {$tabela} ALTER COLUMN tenant_id SET NOT NULL");
            DB::statement(
                "ALTER TABLE {$tabela}
                   ADD CONSTRAINT {$tabela}_tenant_id_fk
                   FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE RESTRICT"
            );
            DB::statement("ALTER TABLE {$tabela} ENABLE ROW LEVEL SECURITY");
            DB::statement("ALTER TABLE {$tabela} FORCE ROW LEVEL SECURITY");
            DB::statement(
                "CREATE POLICY {$tabela}_por_tenant ON {$tabela}
                     USING (tenant_id = {$contexto})
                     WITH CHECK (tenant_id = {$contexto})"
            );
        }

        // O papel `professor`. `jogador` é o aluno e `mestre` é quem administra o
        // conteúdo da escola — os nomes do jogo, mantidos porque renomeá-los seria churn
        // sem valor de produto. O `platform_admin` do roteiro v1 NÃO entra: ele lê
        // através das instituições, e o RLS existe justamente para impedir isso.
        DB::statement('ALTER TABLE usuarios DROP CONSTRAINT usuarios_papel_check');
        DB::statement(
            "ALTER TABLE usuarios ADD CONSTRAINT usuarios_papel_check
                 CHECK (papel IN ('jogador', 'professor', 'mestre'))"
        );

        // Redundante desde a Etapa C: `usuarios` já pertence a um tenant.
        Schema::dropIfExists('tenant_membros');
    }

    public function down(): void
    {
        Schema::dropIfExists('turma_professores');
        Schema::dropIfExists('matriculas');
        Schema::dropIfExists('turmas');

        DB::statement('ALTER TABLE usuarios DROP CONSTRAINT usuarios_papel_check');
        DB::statement(
            "ALTER TABLE usuarios ADD CONSTRAINT usuarios_papel_check
                 CHECK (papel IN ('jogador', 'mestre'))"
        );
    }
};

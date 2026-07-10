<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Quem administra a plataforma — e não uma escola. Etapa E, console do operador.
 *
 * **Por que uma tabela nova, e não um papel em `usuarios`.** Desde a Etapa C, `usuarios` é
 * tenant-scoped: uma conta pertence a exatamente uma instituição, e o RLS a esconde de
 * todas as outras. Um "administrador da plataforma" dentro de `usuarios` seria um usuário
 * de alguma escola — a primeira, por acidente histórico — com poder sobre as demais. O
 * `platform_admin` que a Etapa D.2 recusou.
 *
 * O operador não pertence a instituição nenhuma. Ele mora num catálogo global, como
 * `tenants` e `tenant_dominios`, e por isso esta tabela **não tem `tenant_id` nem RLS**. Ele
 * também não ganha poder de banco: continua sendo o papel `algorithmia_app`, sujeito às
 * policies. O que ele pode fazer é escolher em qual instituição entrar, uma de cada vez —
 * exatamente o que uma requisição HTTP faz com o `Host`.
 *
 * **`operadores` e `usuarios` nunca se misturam.** São guards diferentes: um mestre não
 * entra no console, e um operador não entra no jogo. Não há caminho de código entre eles.
 *
 * O console só existe se `CONSOLE_HOST` estiver preenchido. Sem ele, as rotas nem são
 * registradas — e um `/console` responde 404 em qualquer domínio.
 *
 * `auditoria.autor_tipo` entra aqui porque agora há duas origens de autor, e os ids das
 * duas tabelas colidem: o operador 3 e o aluno 3 são pessoas diferentes. Sem a coluna, uma
 * linha de auditoria não sabe dizer quem agiu.
 *
 * Aditiva: tabela nova, coluna nova e anulável.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('operadores', function (Blueprint $table): void {
            $table->id();
            $table->string('nome', 80);
            $table->string('email', 150);
            $table->string('senha_hash', 255);

            // Revogar o acesso de alguém que saiu da equipe não pode exigir apagar as
            // linhas de auditoria que ele assinou.
            $table->boolean('ativo')->default(true);

            $table->timestamp('ultimo_acesso_em')->nullable();
            $table->timestamps();
        });

        // Mesma regra de `usuarios`: `Fulano@` e `fulano@` são a mesma pessoa.
        DB::statement('CREATE UNIQUE INDEX operadores_email_unico ON operadores (lower(email))');

        Schema::table('auditoria', function (Blueprint $table): void {
            $table->string('autor_tipo', 20)->nullable()->after('autor_id');
        });

        // As linhas que já existem foram todas escritas por usuários: o console não existia.
        DB::table('auditoria')->whereNull('autor_tipo')->whereNotNull('autor_id')->update(['autor_tipo' => 'usuario']);
    }

    public function down(): void
    {
        Schema::table('auditoria', function (Blueprint $table): void {
            $table->dropColumn('autor_tipo');
        });

        Schema::dropIfExists('operadores');
    }
};

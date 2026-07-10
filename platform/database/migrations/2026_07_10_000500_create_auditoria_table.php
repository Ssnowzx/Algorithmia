<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Trilha de auditoria das ações administrativas.
 *
 * Hoje um mestre exclui uma fase — e, em cascata, todos os seus desafios — sem deixar
 * rastro de quem foi, quando, e o que sumiu. O jogo tem conteúdo criado à mão pela
 * equipe, fora do git: uma exclusão errada não tem de onde voltar.
 *
 * Três decisões que valem explicação:
 *
 * 1. **Nenhuma chave estrangeira para o alvo.** A linha documenta algo que foi
 *    apagado; um `constrained()` a apagaria junto, no exato caso em que ela importa.
 *
 * 2. **Nenhuma chave estrangeira para o autor**, e o e-mail dele copiado. Apagar a
 *    conta de quem agiu não pode apagar o registro de que agiu.
 *
 * 3. **Sem `updated_at`.** Uma linha de auditoria que pode ser editada não é
 *    auditoria. As gravações são só INSERT.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('auditoria', function (Blueprint $table): void {
            $table->id();

            // Sem `constrained()`: ver (2) no cabeçalho.
            $table->unsignedBigInteger('autor_id')->nullable();
            $table->string('autor_email', 255)->nullable();

            // 'fase.excluir', 'desafio.atualizar', 'item.criar'…
            $table->string('acao', 60);

            $table->string('alvo_tipo', 40);
            $table->unsignedBigInteger('alvo_id')->nullable();

            // O que havia ali. Num delete, é a única cópia que sobra.
            $table->jsonb('resumo')->nullable();

            $table->string('ip', 45)->nullable();

            // O mesmo que sai no cabeçalho X-Request-Id e na linha de log.
            $table->uuid('request_id')->nullable();

            $table->timestamp('created_at')->useCurrent();

            $table->index('created_at');
            $table->index('acao');
            $table->index(['alvo_tipo', 'alvo_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auditoria');
    }
};

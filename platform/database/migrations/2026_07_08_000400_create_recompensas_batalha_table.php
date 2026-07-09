<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Chave de idempotência da recompensa de batalha.
 *
 * No legado, a proteção contra duplo-crédito é o flag `recompensado` gravado em
 * `$_SESSION['batalha']`. Isso significa que a garantia dura o que dura a sessão:
 * duas requisições concorrentes, um replay do POST de vitória ou uma sessão
 * restaurada de outro lugar creditam a recompensa de novo. O teste
 * `tests/Motor/RecompensaTest.php` do legado registra o efeito: a reputação
 * duplica (XP e ouro escapam por acidente, gravados como valor absoluto).
 *
 * Aqui a garantia passa para o banco. Cada batalha nasce com um identificador; a
 * concessão insere essa chave e só credita se a inserção foi inédita. É a mesma
 * transação, então uma falha no meio desfaz a marca junto com o crédito.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recompensas_batalha', function (Blueprint $table): void {
            // Gerado pelo motor ao iniciar a batalha; não vem do cliente.
            $table->uuid('batalha_id')->primary();
            $table->foreignId('personagem_id')->constrained('personagens')->cascadeOnDelete();
            $table->foreignId('fase_id')->constrained('fases')->cascadeOnDelete();
            $table->timestamp('concedida_em')->useCurrent();

            $table->index('personagem_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recompensas_batalha');
    }
};

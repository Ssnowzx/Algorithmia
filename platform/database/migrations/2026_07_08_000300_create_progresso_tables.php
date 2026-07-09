<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * O que o jogador acumula: inventário, progresso, conquistas, escolhas e o log
 * de respostas que alimenta maestria, missões e o anti-repetição do sorteio.
 *
 * As chaves únicas aqui não são decoração: são os alvos do `ON CONFLICT` que
 * substitui o `INSERT ... ON DUPLICATE KEY UPDATE` e o `INSERT IGNORE` do MySQL.
 * - `inventario (personagem_id, item_id)`      → Inventario::adicionar
 * - `progresso_fases (personagem_id, fase_id)` → ProgressoFase::registrar
 * - `conquistas_personagem` (PK composta)      → Conquista::conceder
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventario', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('personagem_id')->constrained('personagens')->cascadeOnDelete();
            $table->foreignId('item_id')->constrained('itens')->cascadeOnDelete();
            $table->integer('quantidade')->default(1);
            $table->boolean('equipado')->default(false);

            $table->unique(['personagem_id', 'item_id'], 'uq_inv');
        });

        Schema::create('progresso_fases', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('personagem_id')->constrained('personagens')->cascadeOnDelete();
            $table->foreignId('fase_id')->constrained('fases')->cascadeOnDelete();
            $table->smallInteger('estrelas')->default(0);
            $table->integer('acertos')->default(0);
            $table->integer('erros')->default(0);
            $table->boolean('usou_ia')->default(false);
            $table->timestamp('concluida_em')->useCurrent();

            $table->unique(['personagem_id', 'fase_id'], 'uq_prog');
        });

        Schema::create('conquistas_personagem', function (Blueprint $table): void {
            $table->foreignId('personagem_id')->constrained('personagens')->cascadeOnDelete();
            $table->foreignId('conquista_id')->constrained('conquistas')->cascadeOnDelete();
            $table->timestamp('obtida_em')->useCurrent();

            $table->primary(['personagem_id', 'conquista_id']);
        });

        Schema::create('escolhas', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('personagem_id')->constrained('personagens')->cascadeOnDelete();
            $table->string('codigo', 60);
            $table->string('valor', 120);
            $table->timestamp('criado_em')->useCurrent();
        });

        Schema::create('respostas_log', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('personagem_id')->constrained('personagens')->cascadeOnDelete();
            $table->foreignId('desafio_id')->constrained('desafios')->cascadeOnDelete();
            $table->boolean('correta');
            $table->boolean('usou_ia')->default(false);
            $table->timestamp('respondido_em')->useCurrent();

            // Desafio::idsVistos() cruza personagem × fase a cada início de batalha.
            $table->index(['personagem_id', 'desafio_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('respostas_log');
        Schema::dropIfExists('escolhas');
        Schema::dropIfExists('conquistas_personagem');
        Schema::dropIfExists('progresso_fases');
        Schema::dropIfExists('inventario');
    }
};

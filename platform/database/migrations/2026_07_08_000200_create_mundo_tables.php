<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * O mundo jogável: personagens, fases do mapa, desafios e diálogos.
 *
 * `personagens.atualizado_em` era `ON UPDATE CURRENT_TIMESTAMP` no MySQL. O
 * PostgreSQL não tem esse construto e `useCurrentOnUpdate()` do Laravel é um
 * no-op fora do MySQL. Quem passa a manter a coluna é o Eloquent, via
 * `const UPDATED_AT = 'atualizado_em'` no model — escrever direto por SQL cru
 * deixaria a coluna parada.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('personagens', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('usuario_id')->unique()->constrained('usuarios')->cascadeOnDelete();
            $table->string('nome', 80);
            $table->enum('classe', ['mago', 'guerreiro', 'ranger', 'xeno', 'elfo', 'draconato']);
            $table->integer('nivel')->default(1);
            $table->integer('xp')->default(0);
            $table->integer('hp_max')->default(100);
            $table->integer('hp_atual')->default(100);
            $table->integer('mp_max')->default(40);
            $table->integer('mp_atual')->default(40);
            $table->integer('ouro')->default(50);
            $table->integer('reputacao')->default(0);
            $table->integer('capitulo')->default(0);
            $table->timestamp('criado_em')->useCurrent();
            $table->timestamp('atualizado_em')->useCurrent();
        });

        Schema::create('fases', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('mestre_id')->nullable()->constrained('mestres')->nullOnDelete();
            $table->integer('ordem_global');
            $table->string('nome', 150);
            $table->enum('tipo', ['historia', 'licao', 'chefe', 'chefe_final', 'secundaria'])->default('licao');
            $table->text('descricao')->nullable();
            $table->string('inimigo_nome', 120)->nullable();
            $table->string('inimigo_svg', 80)->nullable();
            $table->integer('inimigo_hp')->default(60);
            $table->integer('inimigo_ataque')->default(10);
            $table->integer('xp_recompensa')->default(50);
            $table->integer('ouro_recompensa')->default(20);
            $table->foreignId('item_drop_id')->nullable()->constrained('itens')->nullOnDelete();
            // Auto-referência: a fase anterior que destrava esta.
            $table->foreignId('requisito_fase_id')->nullable()->constrained('fases')->nullOnDelete();
        });

        Schema::create('desafios', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('fase_id')->constrained('fases')->cascadeOnDelete();
            $table->integer('ordem')->default(0);
            $table->enum('tipo', ['multipla', 'vf', 'completar', 'erro', 'ordenar', 'arrastar']);
            $table->enum('assunto', ['php', 'mvc', 'sql', 'poo', 'estruturas', 'redes', 'logica', 'calculo']);
            $table->text('pergunta');
            $table->text('codigo')->nullable();
            $table->jsonb('opcoes')->nullable();
            // O gabarito nunca sai do servidor: BatalhaService::estadoPublico() o remove.
            $table->jsonb('resposta');
            $table->text('explicacao');
            $table->smallInteger('dificuldade')->default(1);

            // O sorteio anti-repetição lê o pool inteiro da fase, ordenado.
            $table->index(['fase_id', 'ordem']);
        });

        Schema::create('dialogos', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('fase_id')->constrained('fases')->cascadeOnDelete();
            $table->enum('momento', ['antes', 'vitoria', 'derrota'])->default('antes');
            $table->enum('variante', ['padrao', 'ia'])->default('padrao');
            $table->integer('ordem')->default(0);
            $table->string('falante', 80);
            $table->string('svg_slug', 80)->nullable();
            $table->text('texto');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dialogos');
        Schema::dropIfExists('desafios');
        Schema::dropIfExists('fases');
        Schema::dropIfExists('personagens');
    }
};

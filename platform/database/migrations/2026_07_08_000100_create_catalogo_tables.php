<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Tabelas sem dependências: contas, mestres, catálogo de itens e de conquistas.
 *
 * Notas de tradução MySQL → PostgreSQL (ver docs/migracao/INVENTARIO.md §6.1):
 *
 * - `ENUM(...)` vira varchar + CHECK, que é o que `$table->enum()` gera no
 *   PostgreSQL. Estender os valores depois exige recriar o CHECK — no MySQL
 *   bastava um ALTER. Duas colunas já foram estendidas por migration no legado
 *   (`personagens.classe` e `desafios.assunto`), então isso vai acontecer.
 * - `AUTO_INCREMENT` vira `bigserial` via `$table->id()`. Os IDs do legado são
 *   preservados na importação: `ConquistaService` referencia fases por ID fixo.
 * - `TINYINT(1)` vira `boolean` de verdade.
 * - A collation `utf8mb4_unicode_ci` do MySQL tornava o UNIQUE de e-mail
 *   insensível a maiúsculas; no PostgreSQL não é. Em vez da extensão `citext`
 *   (que exigiria superusuário no deploy), o índice único é sobre `lower(email)`.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usuarios', function (Blueprint $table): void {
            $table->id();
            $table->string('nome', 80);
            $table->string('email', 150);
            $table->string('senha_hash');
            $table->enum('papel', ['jogador', 'mestre'])->default('jogador');
            $table->timestamp('criado_em')->useCurrent();
        });

        // Preserva a semântica case-insensitive que a collation do MySQL dava de graça.
        DB::statement('CREATE UNIQUE INDEX usuarios_email_unique ON usuarios (lower(email))');

        Schema::create('mestres', function (Blueprint $table): void {
            $table->id();
            $table->string('nome', 120);
            $table->string('titulo', 120);
            $table->string('disciplina', 120);
            $table->string('regiao', 120);
            $table->text('historia')->nullable();
            $table->text('personalidade')->nullable();
            $table->string('bordao')->nullable();
            $table->string('svg_slug', 80);
            $table->string('cor_tema', 7)->default('#7c5cff');
            $table->integer('ordem')->default(0);
        });

        Schema::create('itens', function (Blueprint $table): void {
            $table->id();
            $table->string('nome', 120);
            $table->text('descricao')->nullable();
            $table->enum('tipo', ['arma', 'escudo', 'acessorio', 'pocao', 'especial']);
            // jsonb (e não json): permite índice e comparação; o jogo lê `efeito`
            // a cada golpe para somar bônus de ataque e defesa.
            $table->jsonb('efeito')->nullable();
            $table->integer('preco')->default(0);
            $table->string('svg_slug', 80)->default('item-generico');
            $table->enum('raridade', ['comum', 'raro', 'epico', 'lendario'])->default('comum');
            $table->boolean('compravel')->default(true);
        });

        Schema::create('conquistas', function (Blueprint $table): void {
            $table->id();
            $table->string('codigo', 60)->unique();
            $table->string('nome', 120);
            $table->string('descricao');
            $table->string('svg_slug', 80)->default('conquista-generica');
            $table->boolean('secreta')->default(false);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conquistas');
        Schema::dropIfExists('itens');
        Schema::dropIfExists('mestres');
        Schema::dropIfExists('usuarios');
    }
};

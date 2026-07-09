<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Sessão em banco, e não em cookie.
 *
 * O estado da batalha carrega os desafios completos — com gabarito — para que a
 * correção aconteça no servidor. Isso não cabe nos 4 KB de um cookie, e mandá-lo
 * ao cliente entregaria as respostas. Ver docs/migracao/PLANO.md §5.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sessions', function (Blueprint $table): void {
            $table->string('id')->primary();
            // Sem chave estrangeira: o Laravel escreve a sessão de visitantes
            // anônimos e de usuários já removidos.
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->text('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sessions');
    }
};

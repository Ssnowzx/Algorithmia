<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class HealthzTest extends TestCase
{
    #[Test]
    public function deve_responder_ok_quando_o_banco_atende(): void
    {
        // ARRANGE + ACT
        $resposta = $this->getJson('/healthz');

        // ASSERT
        $resposta->assertOk()->assertExactJson([
            'status' => 'ok',
            'servicos' => ['app' => 'ok', 'banco' => 'ok'],
        ]);
    }

    #[Test]
    public function deve_responder_503_quando_o_banco_esta_fora(): void
    {
        // ARRANGE: aponta a conexão para uma porta onde nada escuta.
        config(['database.connections.pgsql.port' => 1]);
        DB::purge('pgsql');

        // ACT
        $resposta = $this->getJson('/healthz');

        // ASSERT: o balanceador precisa ver 503, não um 200 mentiroso.
        $resposta->assertStatus(503)->assertJsonPath('servicos.banco', 'fora');
    }
}

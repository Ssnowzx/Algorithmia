<?php

declare(strict_types=1);

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * A conexão `legado` só é usada por `algorithmia:importar`, no dia do corte. Estes
 * testes olham o `config/database.php` de verdade — de propósito. O
 * `ImportacaoDoLegadoTest` substitui a conexão por um SQLite de mentira (a CI não tem
 * MySQL), e portanto não pode provar nada sobre a forma real do bloco.
 */
final class ConexaoLegadoTest extends TestCase
{
    /**
     * Num host compartilhado, abrir o MySQL do legado em TCP para a faixa `172.x` o
     * entrega aos containers de todos os outros tenants da máquina. O caminho seguro é
     * montar o socket no container — e para isso a chave precisa existir.
     */
    #[Test]
    public function a_conexao_legado_oferece_socket_unix(): void
    {
        // ARRANGE + ACT
        $legado = config('database.connections.legado');

        // ASSERT
        $this->assertIsArray($legado);
        $this->assertArrayHasKey('unix_socket', $legado);
    }

    /**
     * A armadilha: `MySqlConnector::hasSocket()` é `isset() && ! empty()`. Um padrão
     * `null` seria seguro, e um padrão `''` também — mas trocar o `''` por um `env()`
     * sem padrão faria uma chave vazia no `.env` produzir o DSN
     * `mysql:unix_socket=;dbname=…`, que falha com um erro que não menciona socket
     * nenhum. Vazio TEM de cair em TCP.
     */
    #[Test]
    public function socket_vazio_cai_em_tcp_e_nao_vira_dsn_quebrado(): void
    {
        // ARRANGE + ACT
        $legado = config('database.connections.legado');

        // ASSERT: sem LEGADO_DB_SOCKET no ambiente, o padrão é vazio…
        $this->assertSame('', $legado['unix_socket']);

        // …e é assim que o Laravel decide entre socket e TCP.
        $this->assertTrue(empty($legado['unix_socket']), 'vazio precisa cair em TCP');

        // ASSERT: e o caminho TCP continua configurado para quem não pode usar socket.
        $this->assertNotEmpty($legado['host']);
        $this->assertNotEmpty($legado['port']);
    }
}

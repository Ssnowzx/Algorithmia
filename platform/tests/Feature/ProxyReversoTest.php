<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Confiança em proxy reverso.
 *
 * Durante a janela de coexistência, o `httpd` do jogo antigo termina o TLS e repassa
 * a requisição ao nginx do port. Se o Laravel não confiar nesse proxy, ele acha que
 * a conexão é `http`: gera URLs `http://` e nunca envia o cookie `secure` — o
 * jogador loga, o cookie não volta, e ele cai na tela de login de novo. Para sempre.
 *
 * O oposto também é perigoso: confiar em qualquer origem deixa um cliente forjar
 * `X-Forwarded-Proto` e `X-Forwarded-Host`. Por isso a lista vem do ambiente e é
 * **vazia por padrão**.
 */
final class ProxyReversoTest extends TestCase
{
    #[Test]
    public function sem_a_variavel_de_ambiente_nenhum_proxy_e_confiado(): void
    {
        // ARRANGE: é o estado padrão — nada em TRUSTED_PROXIES.
        $requisicao = Request::create('http://algorithmia.test/mapa', server: [
            'REMOTE_ADDR' => '10.0.0.7',
        ]);
        $requisicao->headers->set('X-Forwarded-Proto', 'https');

        // ACT + ASSERT: o cabeçalho forjado é ignorado.
        $this->assertFalse($requisicao->isSecure());
        $this->assertSame([], Request::getTrustedProxies());
    }

    #[Test]
    public function com_o_proxy_confiado_o_esquema_https_e_respeitado(): void
    {
        // ARRANGE: é o que o entrypoint faz ao ler TRUSTED_PROXIES do .env.producao.
        Request::setTrustedProxies(['10.0.0.7'], Request::HEADER_X_FORWARDED_FOR | Request::HEADER_X_FORWARDED_PROTO);

        $requisicao = Request::create('http://algorithmia.test/mapa', server: [
            'REMOTE_ADDR' => '10.0.0.7',
        ]);
        $requisicao->headers->set('X-Forwarded-Proto', 'https');

        // ACT + ASSERT
        $this->assertTrue($requisicao->isSecure(), 'sem isto, o cookie secure nunca é enviado');

        Request::setTrustedProxies([], 0);
    }

    #[Test]
    public function um_cliente_que_nao_e_o_proxy_nao_forja_o_esquema(): void
    {
        // ARRANGE: o proxy confiado é 10.0.0.7; quem chega é outro.
        Request::setTrustedProxies(['10.0.0.7'], Request::HEADER_X_FORWARDED_FOR | Request::HEADER_X_FORWARDED_PROTO);

        $requisicao = Request::create('http://algorithmia.test/mapa', server: [
            'REMOTE_ADDR' => '203.0.113.9',
        ]);
        $requisicao->headers->set('X-Forwarded-Proto', 'https');

        // ACT + ASSERT
        $this->assertFalse($requisicao->isSecure());

        Request::setTrustedProxies([], 0);
    }

    #[Test]
    public function o_valor_http_no_cabecalho_nao_e_lido_como_seguro(): void
    {
        // O nginx chegou a repassar `fastcgi_param HTTPS $http_x_forwarded_proto`.
        // O Symfony considera segura qualquer variável HTTPS não-vazia e diferente
        // de "off" — o literal "http" seria lido como HTTPS. Este teste trava a
        // decisão de deixar o esquema para o `trustProxies`.
        Request::setTrustedProxies(['10.0.0.7'], Request::HEADER_X_FORWARDED_FOR | Request::HEADER_X_FORWARDED_PROTO);

        $requisicao = Request::create('http://algorithmia.test/mapa', server: [
            'REMOTE_ADDR' => '10.0.0.7',
        ]);
        $requisicao->headers->set('X-Forwarded-Proto', 'http');

        $this->assertFalse($requisicao->isSecure());

        Request::setTrustedProxies([], 0);
    }
}

<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

/**
 * Content-Security-Policy com `nonce` por requisição.
 *
 * O comentário do nginx prometia um CSP desde sempre — "barato aqui e caro depois" —
 * e nenhum header existia. Ele não podia existir lá: o `nonce` muda a cada resposta,
 * e o nginx não o conhece. Quem monta o HTML monta a política.
 *
 * Sem `nonce`, a única forma de permitir o `window.BATALHA` da arena seria
 * `script-src 'unsafe-inline'`, que desliga exatamente a proteção que o CSP oferece.
 */
final class AplicarPoliticaDeConteudo
{
    public function handle(Request $requisicao, Closure $proximo): Response
    {
        $nonce = base64_encode(random_bytes(16));

        // As views leem `$cspNonce`; o container o entrega a quem precisar dele.
        View::share('cspNonce', $nonce);
        app()->instance('csp.nonce', $nonce);

        /** @var Response $resposta */
        $resposta = $proximo($requisicao);

        $resposta->headers->set('Content-Security-Policy', $this->politica($nonce));

        return $resposta;
    }

    private function politica(string $nonce): string
    {
        return implode('; ', [
            // Nada é permitido por omissão.
            "default-src 'none'",

            "script-src 'self' 'nonce-{$nonce}'",

            // `unsafe-inline` só para estilo, e é uma dívida consciente: nove views
            // usam atributo `style=` para posicionar barras de HP e medidores. Um
            // `nonce` não alcança atributos, só elementos `<style>`. O risco de um
            // atributo de estilo é exfiltração por seletor, não execução de código.
            "style-src 'self' 'unsafe-inline'",

            // Sem `data:`: verificado que nem as views, nem o CSS, nem o helper `Arte`
            // produzem data-URI. A arte é servida como arquivo, de `/img`. Permitir
            // `data:` aqui abriria um canal de exfiltração por imagem, de graça.
            "img-src 'self'",

            // Nenhuma fonte externa: as `@font-face` apontam para `/fonts` do próprio
            // domínio. Se algum dia entrar um Google Fonts, este teste quebra primeiro.
            "font-src 'self'",

            // Os turnos de batalha vão por fetch() para o próprio domínio.
            "connect-src 'self'",

            "form-action 'self'",
            "base-uri 'none'",
            "frame-ancestors 'none'",
            "object-src 'none'",
        ]);
    }
}

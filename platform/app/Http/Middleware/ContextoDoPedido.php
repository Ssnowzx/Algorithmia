<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

/**
 * Dá um identificador a cada requisição, e o cola em tudo o que ela produzir.
 *
 * Sem isto, investigar um incidente é ler um log em que as linhas de trinta jogadores
 * simultâneos estão intercaladas, sem nada que as separe. Com isto, o mesmo
 * `request_id` aparece na linha de log, no cabeçalho da resposta e na linha de
 * auditoria — e um relato de "excluí a fase errada às 14h" vira uma consulta.
 *
 * O identificador é gerado aqui e nunca lido do cliente: aceitá-lo de um cabeçalho
 * deixaria qualquer um forjar a trilha, ou colidir com a de outro.
 */
final class ContextoDoPedido
{
    public const CABECALHO = 'X-Request-Id';

    public function handle(Request $requisicao, Closure $proximo): Response
    {
        $id = (string) Str::uuid();

        // Quem precisar dele no meio da requisição (a auditoria, por exemplo).
        app()->instance('pedido.id', $id);

        Log::shareContext([
            'request_id' => $id,
            'usuario_id' => $requisicao->user()?->id,
            'ip' => $requisicao->ip(),
            'rota' => $requisicao->route()?->getName(),
        ]);

        /** @var Response $resposta */
        $resposta = $proximo($requisicao);

        // O jogador que abre um chamado pode copiar isto da aba de rede.
        $resposta->headers->set(self::CABECALHO, $id);

        return $resposta;
    }
}

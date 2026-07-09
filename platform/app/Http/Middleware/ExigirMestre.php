<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\Usuario;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Restringe o Painel do Mestre a quem tem papel 'mestre'.
 *
 * Devolve 403, e não um redirecionamento silencioso: um jogador que descobriu a
 * URL precisa saber que ela existe e não é dele. Esconder o painel atrás de um
 * redirect é segurança por obscuridade, e o `papel` já está no banco.
 */
final class ExigirMestre
{
    public function handle(Request $requisicao, Closure $proximo): Response
    {
        $usuario = Auth::user();

        if (! $usuario instanceof Usuario || ! $usuario->ehMestre()) {
            throw new AccessDeniedHttpException('Só os Mestres passam por aqui.');
        }

        return $proximo($requisicao);
    }
}

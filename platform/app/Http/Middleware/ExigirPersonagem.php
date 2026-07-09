<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\Personagem;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Garante que quem chegou já criou seu herói, e o injeta na requisição.
 *
 * Toda rota de jogo depende de um personagem. Sem esta guarda, cada controller
 * repetiria a mesma checagem — e bastaria esquecê-la uma vez para expor um erro
 * fatal a quem acabou de se registrar.
 */
final class ExigirPersonagem
{
    public function handle(Request $requisicao, Closure $proximo): Response
    {
        $personagem = Personagem::query()->where('usuario_id', Auth::id())->first();

        if ($personagem === null) {
            return redirect()->route('personagem.criar');
        }

        $requisicao->attributes->set('personagem', $personagem);

        return $proximo($requisicao);
    }
}
